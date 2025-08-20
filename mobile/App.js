import React, { useEffect, useState } from 'react';
import { View, Text, FlatList } from 'react-native';

export default function App() {
  const [fields, setFields] = useState([]);

  useEffect(() => {
    fetch('http://localhost:8000/api/fields')
      .then((res) => res.json())
      .then((json) => setFields(json.data || []))
      .catch((err) => console.log(err));
  }, []);

  return (
    <View style={{ flex: 1, paddingTop: 50, paddingHorizontal: 16 }}>
      <Text style={{ fontSize: 20, fontWeight: 'bold', marginBottom: 12 }}>
        Canchas disponibles
      </Text>
      <FlatList
        data={fields}
        keyExtractor={(item) => item.id.toString()}
        renderItem={({ item }) => (
          <Text>{item.name} - {item.sport}</Text>
        )}
        ListEmptyComponent={<Text>No hay canchas</Text>}
      />
    </View>
  );
}
