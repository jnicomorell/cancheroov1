import React, { useContext } from 'react';
import { View, Button, Alert } from 'react-native';
import { AuthContext } from '../../context/AuthContext';

export default function AcceptInvitationScreen({ route }) {
  const { token } = useContext(AuthContext);
  const reservationId = route.params?.id;

  const confirm = () => {
    fetch(`http://localhost:8000/api/reservations/${reservationId}/confirm`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${token}` },
    })
      .then((res) => res.json())
      .then(() => Alert.alert('Participación confirmada'))
      .catch(() => Alert.alert('Error al confirmar'));
  };

  return (
    <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
      <Button title="Confirmar asistencia" onPress={confirm} />
    </View>
  );
}
