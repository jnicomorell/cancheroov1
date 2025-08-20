import './bootstrap';
import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {
    const list = document.getElementById('reservations');
    if (list) {
        axios.get('/api/reservations').then(res => {
            res.data.forEach(r => {
                const li = document.createElement('li');
                li.textContent = `${r.field.name} - ${r.payment_status}`;
                list.appendChild(li);
            });
        });
    }
});
