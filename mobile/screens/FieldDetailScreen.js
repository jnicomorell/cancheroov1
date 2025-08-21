import React, { useContext, useEffect, useState } from 'react';
import { View, Text, Button, Alert, Share } from 'react-native';
import { AuthContext } from '../context/AuthContext';

export default function FieldDetailScreen({ route }) {
  const { id } = route.params;
  const { token } = useContext(AuthContext);
  const [field, setField] = useState(null);

  useEffect(() => {
    fetch(`http://localhost:8000/api/fields/${id}`)
      .then((res) => res.json())
      .then(setField)
      .catch((err) => console.log(err));
  }, [id]);

  const handleReserve = () => {
    const now = new Date();
    const end = new Date(now.getTime() + 60 * 60 * 1000);
    fetch('http://localhost:8000/api/reservations', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${token}`,
      },
      body: JSON.stringify({
        field_id: id,
        start_time: now.toISOString(),
        end_time: end.toISOString(),
      }),
    })
      .then((res) => res.json())
      .then(() => Alert.alert('Reserva creada'))
      .catch(() => Alert.alert('Error al reservar'));
  };

  const handleShare = () => {
    Share.share({ message: `Mira esta cancha: ${field.name}` });
  };

  if (!field) {
    return (
      <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
        <Text>Cargando...</Text>
      </View>
    );
  }

  return (
    <View style={{ flex: 1, padding: 16 }}>
      <Text style={{ fontSize: 24, marginBottom: 8 }}>{field.name}</Text>
      <Text>Deporte: {field.sport}</Text>
      <Text>Precio: ${field.price_per_hour} / hora</Text>
      <Text>
        Promedio de calificaciones: {field.average_rating ? field.average_rating.toFixed(1) : 'N/A'}
      </Text>
      <View style={{ marginVertical: 20 }}>
        <Button title="Reservar ahora" onPress={handleReserve} />
      </View>
      <View style={{ marginBottom: 20 }}>
        <Button title="Compartir" onPress={handleShare} />
      </View>
      <Text>Pago, notificaciones y calificaciones estarán disponibles próximamente.</Text>
    </View>
  );
}
