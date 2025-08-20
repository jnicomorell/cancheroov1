import React, { useContext, useEffect, useState } from 'react';
import { View, Text, FlatList, Button, Alert } from 'react-native';
import { AuthContext } from '../context/AuthContext';
import { useSettings } from '../src/context/SettingsContext';

export default function ReservationsScreen() {
  const { token } = useContext(AuthContext);
  const { t, language, currency } = useSettings();
  const [reservations, setReservations] = useState([]);

  const loadReservations = () => {
    fetch(`http://localhost:8000/api/reservations?lang=${language}&currency=${currency}`, {
      headers: { Authorization: `Bearer ${token}` },
    })
      .then((res) => res.json())
      .then(setReservations)
      .catch(() => {});
  };

  useEffect(() => {
    loadReservations();
  }, [language, currency]);

  const cancelReservation = (id) => {
    fetch(`http://localhost:8000/api/reservations/${id}?lang=${language}`, {
      method: 'DELETE',
      headers: { Authorization: `Bearer ${token}` },
    })
      .then((res) => res.json())
      .then(() => {
        Alert.alert(t('reservation_cancelled'));
        loadReservations();
      })
      .catch(() => Alert.alert(t('cancel_failed')));
  };

  return (
    <View style={{ flex: 1, padding: 16 }}>
      <FlatList
        data={reservations}
        keyExtractor={(item) => item.id.toString()}
        renderItem={({ item }) => (
          <View style={{ marginBottom: 12 }}>
            <Text>{item.field.name} - {item.start_time} - {item.price} {currency}</Text>
            {item.status === 'confirmed' && (
              <Button title={t('cancel')} onPress={() => cancelReservation(item.id)} />
            )}
          </View>
        )}
        ListEmptyComponent={<Text>{t('no_reservations')}</Text>}
      />
    </View>
  );
}
