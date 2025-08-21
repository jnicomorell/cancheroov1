import React, { useEffect, useState } from 'react';
import { View, Text, FlatList } from 'react-native';

export default function RankingScreen() {
  const [users, setUsers] = useState([]);

  useEffect(() => {
    fetch('http://localhost:8000/api/ranking')
      .then((res) => res.json())
      .then(setUsers)
      .catch((err) => console.log(err));
  }, []);

  return (
    <View style={{ flex: 1, padding: 16 }}>
      <FlatList
        data={users}
        keyExtractor={(item) => item.id.toString()}
        renderItem={({ item, index }) => (
          <Text style={{ paddingVertical: 8 }}>
            {index + 1}. {item.name} - {item.points} pts
          </Text>
        )}
        ListEmptyComponent={<Text>No hay usuarios</Text>}
      />
    </View>
  );
}
