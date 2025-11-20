// Fetch rooms + optional tenant names to render the floorplan
async function loadRooms() {
  const res = await fetch('/api/rooms.php', { headers: { 'Accept': 'application/json' }});
  const data = await res.json();
  return data.rooms || [];
}

// Create a room card (green if available, red if occupied)
function mk(room) {
  const card = document.createElement('div');
  card.className = 'room-card ' + (room.status === 'available' ? 'status-available' : 'status-occupied');
  card.setAttribute('data-room', room.room_number);

  const h3 = document.createElement('h3');
  h3.textContent = `Room ${room.room_number}`;
  card.appendChild(h3);

  if (room.tenant_name) {
    const tn = document.createElement('div');
    tn.className = 'tenant-name';
    tn.textContent = `Tenant: ${room.tenant_name}`;
    card.appendChild(tn);
  }

  return card;
}

function mountRooms(rooms) {
  // Split into left (1..10) and right (11..20)
  const left = rooms.filter(r => Number(r.room_number) >= 1 && Number(r.room_number) <= 10)
                    .sort((a,b)=>Number(a.room_number)-Number(b.room_number));
  const right = rooms.filter(r => Number(r.room_number) >= 11 && Number(r.room_number) <= 20)
                     .sort((a,b)=>Number(a.room_number)-Number(b.room_number));

  const leftEl = document.getElementById('left-rooms');
  const rightEl = document.getElementById('right-rooms');

  leftEl.innerHTML = '';
  rightEl.innerHTML = '';

  left.forEach(r => leftEl.appendChild(mk(r)));
  right.forEach(r => rightEl.appendChild(mk(r)));
}

document.addEventListener('DOMContentLoaded', async () => {
  try {
    const rooms = await loadRooms();
    mountRooms(rooms);
  } catch (e) {
    console.error('Failed to load rooms', e);
  }

  // Click-to-select a room (fills any request form input named room_number)
  document.addEventListener('click', (e) => {
    const el = e.target.closest('.room-card');
    if (!el) return;
    const rn = el.getAttribute('data-room');
    const input = document.querySelector('#request-form #room_number, form[action="/request_viewing.php"] input[name="room_number"]');
    if (rn && input) { input.value = rn; }
  });

  // Small inline style for tenant-name (optional)
  document.head.insertAdjacentHTML('beforeend',
    '<style>.tenant-name{font-size:.9rem;opacity:.85;margin-top:.25rem}</style>');
});
