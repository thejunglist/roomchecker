<template>
  <div>
    <h2>Rooms</h2>
    
    <div v-if="loading" class="loading">Loading rooms...</div>
    
    <div v-else-if="error" class="error">{{ error }}</div>
    
    <div v-else class="rooms-grid">
      <div 
        v-for="room in rooms" 
        :key="room.id" 
        class="room-card"
        @click="goToRoom(room.id)"
      >
        <h3>{{ room.room_code }}</h3>
        <p class="building">{{ room.building }}</p>
        <p class="floor">Floor {{ room.floor }}</p>
        <p class="equipment-count">{{ room.equipment_count }} equipment items</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../services/api';

const router = useRouter();
const rooms = ref([]);
const loading = ref(true);
const error = ref('');

const fetchRooms = async () => {
  try {
    const response = await api.get('/rooms');
    rooms.value = response.data;
  } catch (err) {
    error.value = 'Failed to load rooms';
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const goToRoom = (id) => {
  router.push(`/rooms/${id}`);
};

onMounted(() => {
  fetchRooms();
});
</script>

<style scoped>
h2 {
  margin-bottom: 1.5rem;
  color: #2c3e50;
}

.loading, .error {
  text-align: center;
  padding: 2rem;
  color: #666;
}

.error {
  color: #e74c3c;
}

.rooms-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 1.5rem;
}

.room-card {
  background: white;
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
}

.room-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.room-card h3 {
  color: #2c3e50;
  margin-bottom: 0.5rem;
  font-size: 1.5rem;
}

.building {
  color: #666;
  font-size: 0.9rem;
  margin-bottom: 0.25rem;
}

.floor {
  color: #999;
  font-size: 0.85rem;
  margin-bottom: 0.75rem;
}

.equipment-count {
  color: #667eea;
  font-weight: 600;
  font-size: 0.9rem;
}
</style>