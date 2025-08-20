import React, { useEffect, useState } from 'react';
import { View, Text, FlatList } from 'react-native';

export default function App() {
  const [reservations, setReservations] = useState([]);

  useEffect(() => {
    fetch('http://localhost:8000/api/reservations')
      .then((res) => res.json())
      .then((json) => setReservations(json || []))
      .catch((err) => console.log(err));
  }, []);

  return (
    <View style={{ flex: 1, paddingTop: 50, paddingHorizontal: 16 }}>
      <Text style={{ fontSize: 20, fontWeight: 'bold', marginBottom: 12 }}>
        Mis reservas
      </Text>
      <FlatList
        data={reservations}
        keyExtractor={(item) => item.id.toString()}
        renderItem={({ item }) => (
          <Text>{item.field?.name} - {item.payment_status}</Text>
        )}
        ListEmptyComponent={<Text>No hay reservas</Text>}
      />
    </View>
  );
}
