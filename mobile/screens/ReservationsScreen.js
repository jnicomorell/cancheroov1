import React, { useContext, useEffect, useState } from 'react';
import { View, Text, FlatList, Button, Alert } from 'react-native';
import { AuthContext } from '../context/AuthContext';

export default function ReservationsScreen() {
  const { token } = useContext(AuthContext);
  const [reservations, setReservations] = useState([]);

  const loadReservations = () => {
    fetch('http://localhost:8000/api/reservations', {
      headers: { Authorization: `Bearer ${token}` },
    })
      .then((res) => res.json())
      .then(setReservations)
      .catch(() => {});
  };

  useEffect(() => {
    loadReservations();
  }, []);

  const cancelReservation = (id) => {
    fetch(`http://localhost:8000/api/reservations/${id}`, {
      method: 'DELETE',
      headers: { Authorization: `Bearer ${token}` },
    })
      .then((res) => res.json())
      .then(() => {
        Alert.alert('Reserva cancelada');
        loadReservations();
      })
      .catch(() => Alert.alert('No se pudo cancelar'));
  };

  const payReservation = (id) => {
    fetch(`http://localhost:8000/api/reservations/${id}/pay`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${token}` },
    })
      .then((res) => res.json())
      .then(() => {
        Alert.alert('Pago realizado');
        loadReservations();
      })
      .catch(() => Alert.alert('No se pudo pagar'));
  };

  return (
    <View style={{ flex: 1, padding: 16 }}>
      <FlatList
        data={reservations}
        keyExtractor={(item) => item.id.toString()}
        renderItem={({ item }) => (
          <View style={{ marginBottom: 12 }}>
            <Text>{item.field.name} - {item.start_time}</Text>
            {item.weather_alert && (
              <Text style={{ color: 'red' }}>{item.weather_alert}</Text>
            )}
            {item.status === 'confirmed' && (
              <Button title="Cancelar" onPress={() => cancelReservation(item.id)} />
            )}
          </View>
        )}
        ListEmptyComponent={<Text>No hay reservas</Text>}
      />
    </View>
  );
}
