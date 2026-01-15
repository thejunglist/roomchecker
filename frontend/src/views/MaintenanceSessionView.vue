<template>
  <div>
    <button @click="goBack" class="btn-back">← Back to Rooms</button>
    
    <div v-if="loading" class="loading">Loading session...</div>
    
    <div v-else-if="error" class="error">{{ error }}</div>
    
    <div v-else>
      <div class="session-header">
        <div>
          <h2>Maintenance Check - {{ session.room?.room_code }}</h2>
          <p class="session-info">
            Started: {{ formatDate(session.start_time) }} | 
            Technician: {{ session.technician?.first_name }} {{ session.technician?.last_name }}
          </p>
          <span 
            class="status-badge" 
            :class="`status-${session.status}`"
          >
            {{ session.status }}
          </span>
        </div>
        <button 
          v-if="session.status === 'in_progress'"
          @click="completeSession" 
          class="btn-success btn-large"
          :disabled="completing || !allEquipmentChecked"
        >
          {{ completing ? 'Completing...' : 'Complete Session' }}
        </button>
      </div>

      <div v-if="session.status === 'in_progress' && !allEquipmentChecked" class="warning">
        Please check all equipment before completing the session
      </div>
      
      <div class="equipment-checklist">
        <h3>Equipment Checklist ({{ checkedCount }}/{{ equipmentList.length }})</h3>
        
        <div class="progress-bar">
          <div 
            class="progress-fill" 
            :style="{ width: `${progressPercentage}%` }"
          ></div>
        </div>
        
        <div v-if="equipmentList.length === 0" class="no-equipment">
          No equipment to check in this room
        </div>
        
        <div v-else class="equipment-list">
          <div 
            v-for="equipment in equipmentList" 
            :key="equipment.id"
            class="equipment-card"
            :class="{ 'checked': equipment.checked }"
          >
            <div class="equipment-details">
              <h4>{{ equipment.asset_number }}</h4>
              <p class="equipment-type">{{ equipment.equipment_type?.name }}</p>
              <p v-if="equipment.manufacturer" class="equipment-info">
                {{ equipment.manufacturer }} {{ equipment.model }}
              </p>
            </div>
            
            <div v-if="!equipment.checked && session.status === 'in_progress'" class="check-actions">
              <button 
                @click="checkEquipment(equipment, 'pass')" 
                class="btn-check btn-pass"
              >
                ✓ Pass
              </button>
              <button 
                @click="checkEquipment(equipment, 'needs_attention')" 
                class="btn-check btn-attention"
              >
                ⚠ Needs Attention
              </button>
              <button 
                @click="checkEquipment(equipment, 'fail')" 
                class="btn-check btn-fail"
              >
                ✗ Fail
              </button>
            </div>
            
            <div v-else-if="equipment.checked" class="checked-status">
              <span 
                class="check-badge"
                :class="`check-${equipment.checkStatus}`"
              >
                {{ equipment.checkStatus === 'pass' ? '✓ Passed' : 
                   equipment.checkStatus === 'fail' ? '✗ Failed' : 
                   '⚠ Needs Attention' }}
              </span>
              <p v-if="equipment.notes" class="check-notes">
                Note: {{ equipment.notes }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Notes Modal -->
    <div v-if="showNotesModal" class="modal-overlay" @click="closeNotesModal">
      <div class="modal-content" @click.stop>
        <h3>Add Notes (Optional)</h3>
        <p class="modal-equipment">{{ currentEquipment?.asset_number }}</p>
        <textarea 
          v-model="notes" 
          placeholder="Enter any notes about this equipment..."
          rows="4"
        ></textarea>
        <div class="modal-actions">
          <button @click="closeNotesModal" class="btn-cancel">Cancel</button>
          <button @click="submitCheck" class="btn-primary">Submit</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import api from '../services/api';

const router = useRouter();
const route = useRoute();

const session = ref({});
const equipmentList = ref([]);
const loading = ref(true);
const error = ref('');
const completing = ref(false);
const showNotesModal = ref(false);
const currentEquipment = ref(null);
const currentStatus = ref('');
const notes = ref('');

const checkedCount = computed(() => 
  equipmentList.value.filter(e => e.checked).length
);

const progressPercentage = computed(() => 
  equipmentList.value.length ? (checkedCount.value / equipmentList.value.length) * 100 : 0
);

const allEquipmentChecked = computed(() => 
  equipmentList.value.length > 0 && checkedCount.value === equipmentList.value.length
);

const fetchSession = async () => {
  try {
    const response = await api.get(`/maintenance-sessions/${route.params.id}`);
    session.value = response.data;
    
    // Get equipment for this room
    const roomResponse = await api.get(`/rooms/${session.value.room_id}`);
    
    // Mark equipment as checked if there's already a record
    equipmentList.value = roomResponse.data.equipment.map(eq => {
      const record = session.value.maintenance_records?.find(r => r.equipment_id === eq.id);
      return {
        ...eq,
        checked: !!record,
        checkStatus: record?.status,
        notes: record?.notes,
      };
    });
  } catch (err) {
    error.value = 'Failed to load session';
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const checkEquipment = (equipment, status) => {
  currentEquipment.value = equipment;
  currentStatus.value = status;
  notes.value = '';
  showNotesModal.value = true;
};

const submitCheck = async () => {
  try {
    await api.post('/maintenance-records', {
      session_id: session.value.id,
      equipment_id: currentEquipment.value.id,
      status: currentStatus.value,
      notes: notes.value || null,
    });
    
    // Update local state
    const index = equipmentList.value.findIndex(e => e.id === currentEquipment.value.id);
    if (index !== -1) {
      equipmentList.value[index].checked = true;
      equipmentList.value[index].checkStatus = currentStatus.value;
      equipmentList.value[index].notes = notes.value;
    }
    
    closeNotesModal();
  } catch (err) {
    alert('Failed to record check');
    console.error(err);
  }
};

const closeNotesModal = () => {
  showNotesModal.value = false;
  currentEquipment.value = null;
  currentStatus.value = '';
  notes.value = '';
};

const completeSession = async () => {
  if (!confirm('Are you sure you want to complete this session?')) {
    return;
  }
  
  completing.value = true;
  
  try {
    await api.put(`/maintenance-sessions/${session.value.id}`, {
      status: 'completed',
      notes: null,
    });
    
    alert('Session completed successfully!');
    router.push('/');
  } catch (err) {
    alert('Failed to complete session');
    console.error(err);
  } finally {
    completing.value = false;
  }
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  return new Date(dateString).toLocaleString();
};

const goBack = () => {
  router.push('/');
};

onMounted(() => {
  fetchSession();
});
</script>

<style scoped>
.btn-back {
  background: #95a5a6;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  cursor: pointer;
  margin-bottom: 1.5rem;
  font-size: 0.9rem;
}

.btn-back:hover {
  background: #7f8c8d;
}

.loading, .error {
  text-align: center;
  padding: 2rem;
  color: #666;
}

.error {
  color: #e74c3c;
}

.session-header {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  margin-bottom: 1.5rem;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.session-header h2 {
  color: #2c3e50;
  margin-bottom: 0.5rem;
}

.session-info {
  color: #666;
  font-size: 0.9rem;
  margin-bottom: 0.5rem;
}

.status-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.85rem;
  font-weight: 600;
  text-transform: capitalize;
}

.status-in_progress {
  background: #fff3cd;
  color: #856404;
}

.status-completed {
  background: #d4edda;
  color: #155724;
}

.status-abandoned {
  background: #f8d7da;
  color: #721c24;
}

.btn-success {
  background: #27ae60;
  color: white;
  border: none;
  padding: 1rem 2rem;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1.1rem;
}

.btn-success:hover:not(:disabled) {
  background: #229954;
}

.btn-success:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-large {
  padding: 1rem 2rem;
  font-size: 1.1rem;
}

.warning {
  background: #fff3cd;
  color: #856404;
  padding: 1rem;
  border-radius: 4px;
  margin-bottom: 1.5rem;
  text-align: center;
  border: 1px solid #ffeaa7;
}

.equipment-checklist {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.equipment-checklist h3 {
  color: #2c3e50;
  margin-bottom: 1rem;
}

.progress-bar {
  height: 8px;
  background: #e0e0e0;
  border-radius: 4px;
  overflow: hidden;
  margin-bottom: 2rem;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #667eea, #764ba2);
  transition: width 0.3s;
}

.no-equipment {
  text-align: center;
  padding: 2rem;
  color: #999;
}

.equipment-list {
  display: grid;
  gap: 1rem;
}

.equipment-card {
  padding: 1.5rem;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  transition: all 0.3s;
}

.equipment-card.checked {
  border-color: #27ae60;
  background: #f0f9f4;
}

.equipment-details h4 {
  color: #2c3e50;
  margin-bottom: 0.5rem;
  font-size: 1.2rem;
}

.equipment-type {
  color: #667eea;
  font-weight: 600;
  margin-bottom: 0.25rem;
}

.equipment-info {
  color: #666;
  font-size: 0.9rem;
  margin-bottom: 1rem;
}

.check-actions {
  display: flex;
  gap: 0.75rem;
  margin-top: 1rem;
}

.btn-check {
  flex: 1;
  padding: 0.75rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 600;
  font-size: 0.95rem;
  transition: all 0.2s;
}

.btn-pass {
  background: #27ae60;
  color: white;
}

.btn-pass:hover {
  background: #229954;
}

.btn-attention {
  background: #f39c12;
  color: white;
}

.btn-attention:hover {
  background: #e67e22;
}

.btn-fail {
  background: #e74c3c;
  color: white;
}

.btn-fail:hover {
  background: #c0392b;
}

.checked-status {
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #e0e0e0;
}

.check-badge {
  display: inline-block;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  font-weight: 600;
  font-size: 0.9rem;
}

.check-pass {
  background: #d4edda;
  color: #155724;
}

.check-fail {
  background: #f8d7da;
  color: #721c24;
}

.check-needs_attention {
  background: #fff3cd;
  color: #856404;
}

.check-notes {
  margin-top: 0.5rem;
  color: #666;
  font-size: 0.9rem;
  font-style: italic;
}

/* Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  width: 90%;
  max-width: 500px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
}

.modal-content h3 {
  margin-bottom: 1rem;
  color: #2c3e50;
}

.modal-equipment {
  color: #667eea;
  font-weight: 600;
  margin-bottom: 1rem;
  font-size: 1.1rem;
}

.modal-content textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-family: inherit;
  font-size: 0.95rem;
  resize: vertical;
  margin-bottom: 1.5rem;
}

.modal-content textarea:focus {
  outline: none;
  border-color: #667eea;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
}

.btn-cancel {
  background: #95a5a6;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 4px;
  cursor: pointer;
}

.btn-cancel:hover {
  background: #7f8c8d;
}

.btn-primary {
  background: #667eea;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 4px;
  cursor: pointer;
}

.btn-primary:hover {
  background: #5568d3;
}
</style>