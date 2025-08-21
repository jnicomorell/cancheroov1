import React, { useContext, useEffect, useState } from 'react';
import { View, Text, FlatList, Button, Alert } from 'react-native';
import { AuthContext } from '../context/AuthContext';

export default function LoyaltyScreen() {
  const { token } = useContext(AuthContext);
  const [balance, setBalance] = useState(0);
  const [promotions, setPromotions] = useState([]);

  const loadData = () => {
    fetch('http://localhost:8000/api/promotions')
      .then(res => res.json())
      .then(setPromotions)
      .catch(() => {});

    fetch('http://localhost:8000/api/loyalty/balance', {
      headers: { Authorization: `Bearer ${token}` },
    })
      .then(res => res.json())
      .then(json => setBalance(json.balance || 0))
      .catch(() => {});
  };

  useEffect(() => {
    loadData();
  }, []);

  const redeem = (id) => {
    fetch('http://localhost:8000/api/loyalty/redeem', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${token}`,
      },
      body: JSON.stringify({ promotion_id: id }),
    })
      .then(res => res.json())
      .then(json => {
        if (json.balance !== undefined) {
          Alert.alert('Promoción canjeada');
          setBalance(json.balance);
        } else if (json.message) {
          Alert.alert(json.message);
        }
      })
      .catch(() => Alert.alert('No se pudo canjear'));
  };

  return (
    <View style={{ flex: 1, padding: 16 }}>
      <Text>Saldo: {balance} puntos</Text>
      <FlatList
        data={promotions}
        keyExtractor={(item) => item.id.toString()}
        renderItem={({ item }) => (
          <View style={{ marginTop: 12 }}>
            <Text>{item.name} - {item.points_required} pts</Text>
            {balance >= item.points_required && (
              <Button title="Canjear" onPress={() => redeem(item.id)} />
            )}
          </View>
        )}
        ListEmptyComponent={<Text>No hay promociones</Text>}
      />
    </View>
  );
}
