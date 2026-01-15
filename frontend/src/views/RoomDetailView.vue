<template>
  <div>
    <button @click="goBack" class="btn-back">← Back to Rooms</button>
    
    <div v-if="loading" class="loading">Loading room details...</div>
    
    <div v-else-if="error" class="error">{{ error }}</div>
    
    <div v-else>
      <div class="room-header">
        <div>
          <h2>{{ room.room_code }}</h2>
          <p class="building">{{ room.building }} - Floor {{ room.floor }}</p>
          <p v-if="room.description" class="description">{{ room.description }}</p>
        </div>
        <button 
          @click="startMaintenanceSession" 
          class="btn-primary btn-large"
          :disabled="startingSession"
        >
          {{ startingSession ? 'Starting...' : 'Start Maintenance Check' }}
        </button>
      </div>
      
      <div class="equipment-section">
        <h3>Equipment ({{ room.equipment?.length || 0 }} items)</h3>
        
        <div v-if="!room.equipment || room.equipment.length === 0" class="no-equipment">
          No equipment in this room
        </div>
        
        <div v-else class="equipment-list">
          <div 
            v-for="equipment in room.equipment" 
            :key="equipment.id"
            class="equipment-item"
          >
            <div class="equipment-info">
              <h4>{{ equipment.asset_number }}</h4>
              <p class="equipment-type">{{ equipment.equipment_type?.name }}</p>
              <p v-if="equipment.manufacturer" class="equipment-details">
                {{ equipment.manufacturer }} {{ equipment.model }}
              </p>
              <span 
                class="status-badge" 
                :class="`status-${equipment.status}`"
              >
                {{ equipment.status }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import api from '../services/api';

const router = useRouter();
const route = useRoute();

const room = ref({});
const loading = ref(true);
const error = ref('');
const startingSession = ref(false);

const fetchRoom = async () => {
  try {
    const response = await api.get(`/rooms/${route.params.id}`);
    room.value = response.data;
  } catch (err) {
    error.value = 'Failed to load room details';
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const startMaintenanceSession = async () => {
  startingSession.value = true;
  
  try {
    const response = await api.post('/maintenance-sessions', {
      room_id: room.value.id,
    });
    
    // Navigate to maintenance session view
    router.push(`/sessions/${response.data.id}`);
  } catch (err) {
    alert('Failed to start maintenance session');
    console.error(err);
  } finally {
    startingSession.value = false;
  }
};

const goBack = () => {
  router.push('/');
};

onMounted(() => {
  fetchRoom();
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

.room-header {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  margin-bottom: 2rem;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.room-header h2 {
  color: #2c3e50;
  margin-bottom: 0.5rem;
  font-size: 2rem;
}

.building {
  color: #666;
  font-size: 1rem;
  margin-bottom: 0.5rem;
}

.description {
  color: #999;
  font-size: 0.9rem;
  margin-top: 0.5rem;
}

.btn-large {
  padding: 1rem 2rem;
  font-size: 1.1rem;
}

.btn-primary {
  background: #667eea;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 4px;
  cursor: pointer;
  transition: background 0.3s;
  font-size: 1rem;
}

.btn-primary:hover:not(:disabled) {
  background: #5568d3;
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.equipment-section {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.equipment-section h3 {
  color: #2c3e50;
  margin-bottom: 1.5rem;
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

.equipment-item {
  padding: 1.5rem;
  border: 1px solid #e0e0e0;
  border-radius: 6px;
  transition: border-color 0.2s;
}

.equipment-item:hover {
  border-color: #667eea;
}

.equipment-info h4 {
  color: #2c3e50;
  margin-bottom: 0.5rem;
  font-size: 1.2rem;
}

.equipment-type {
  color: #667eea;
  font-weight: 600;
  margin-bottom: 0.25rem;
}

.equipment-details {
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

.status-active {
  background: #d4edda;
  color: #155724;
}

.status-retired {
  background: #f8d7da;
  color: #721c24;
}

.status-in_repair {
  background: #fff3cd;
  color: #856404;
}

.status-in_storage {
  background: #d1ecf1;
  color: #0c5460;
}
</style>