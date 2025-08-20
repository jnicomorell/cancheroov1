import React, { useState, useContext } from 'react';
import { View, TextInput, Button, Alert } from 'react-native';
import { AuthContext } from '../../context/AuthContext';

export default function InvitationScreen({ route }) {
  const { token } = useContext(AuthContext);
  const [userId, setUserId] = useState('');
  const [amount, setAmount] = useState('');

  const sendInvitation = () => {
    fetch(`http://localhost:8000/api/reservations/${route.params?.id}/invite`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${token}`,
      },
      body: JSON.stringify({ user_id: userId, amount }),
    })
      .then((res) => res.json())
      .then(() => Alert.alert('Invitación enviada'))
      .catch(() => Alert.alert('Error al invitar'));
  };

  return (
    <View style={{ flex: 1, padding: 16 }}>
      <TextInput
        placeholder="ID del usuario"
        value={userId}
        onChangeText={setUserId}
        style={{ marginBottom: 12, borderWidth: 1, padding: 8 }}
      />
      <TextInput
        placeholder="Monto"
        value={amount}
        onChangeText={setAmount}
        keyboardType="numeric"
        style={{ marginBottom: 12, borderWidth: 1, padding: 8 }}
      />
      <Button title="Invitar" onPress={sendInvitation} />
    </View>
  );
}
