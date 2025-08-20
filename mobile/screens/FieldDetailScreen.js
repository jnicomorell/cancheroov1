import React, { useContext, useEffect, useState } from 'react';
import { View, Text, Button, Alert } from 'react-native';
import { AuthContext } from '../context/AuthContext';

export default function FieldDetailScreen({ route }) {
  const { id } = route.params;
  const { token } = useContext(AuthContext);
  const [field, setField] = useState(null);
  const [selectedExtras, setSelectedExtras] = useState([]);

  useEffect(() => {
    fetch(`http://localhost:8000/api/fields/${id}`)
      .then((res) => res.json())
      .then(setField)
      .catch((err) => console.log(err));
  }, [id]);

  const toggleExtra = (itemId) => {
    setSelectedExtras((prev) =>
      prev.includes(itemId) ? prev.filter((i) => i !== itemId) : [...prev, itemId]
    );
  };

  const extrasTotal =
    field?.rental_items?.reduce(
      (sum, item) =>
        selectedExtras.includes(item.id) ? sum + item.price : sum,
      0
    ) || 0;

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
        items: selectedExtras.map((itemId) => ({
          rental_item_id: itemId,
          quantity: 1,
        })),
      }),
    })
      .then((res) => res.json())
      .then(() => Alert.alert('Reserva creada'))
      .catch(() => Alert.alert('Error al reservar'));
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
      {field.rental_items && field.rental_items.length > 0 && (
        <View style={{ marginVertical: 20 }}>
          <Text>Extras:</Text>
          {field.rental_items.map((item) => (
            <View key={item.id} style={{ marginVertical: 4 }}>
              <Button
                title={
                  selectedExtras.includes(item.id)
                    ? `Quitar ${item.name} ($${item.price})`
                    : `Agregar ${item.name} ($${item.price})`
                }
                onPress={() => toggleExtra(item.id)}
              />
            </View>
          ))}
        </View>
      )}
      <View style={{ marginVertical: 20 }}>
        <Text>Precio total: ${field.price_per_hour + extrasTotal}</Text>
        <Button title="Reservar ahora" onPress={handleReserve} />
      </View>
      <Text>Pago, notificaciones y calificaciones estarán disponibles próximamente.</Text>
    </View>
  );
}
