import React, { useContext, useEffect, useState } from 'react';
import { View, Text, Button, Alert } from 'react-native';
import { AuthContext } from '../context/AuthContext';
import { useSettings } from '../src/context/SettingsContext';

export default function FieldDetailScreen({ route }) {
  const { id } = route.params;
  const { token } = useContext(AuthContext);
  const { t, language, currency } = useSettings();
  const [field, setField] = useState(null);

  useEffect(() => {
    fetch(`http://localhost:8000/api/fields/${id}?lang=${language}&currency=${currency}`)
      .then((res) => res.json())
      .then(setField)
      .catch((err) => console.log(err));
  }, [id, language, currency]);

  const handleReserve = () => {
    const now = new Date();
    const end = new Date(now.getTime() + 60 * 60 * 1000);
    fetch(`http://localhost:8000/api/reservations?lang=${language}`, {
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
      .then(() => Alert.alert(t('reservation_created')))
      .catch(() => Alert.alert(t('reservation_failed')));
  };

  if (!field) {
    return (
      <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
        <Text>{t('loading')}</Text>
      </View>
    );
  }

  return (
    <View style={{ flex: 1, padding: 16 }}>
      <Text style={{ fontSize: 24, marginBottom: 8 }}>{field.name}</Text>
      <Text>{t('sport')}: {field.sport}</Text>
      <Text>{t('price')}: {field.price_per_hour} {currency} {t('per_hour')}</Text>
      <View style={{ marginVertical: 20 }}>
        <Button title={t('reserve_now')} onPress={handleReserve} />
      </View>
      <Text>{t('coming_soon')}</Text>
    </View>
  );
}
