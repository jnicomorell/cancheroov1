import React, { useEffect, useLayoutEffect, useState } from 'react';
import { View, Text, FlatList, Button, TouchableOpacity } from 'react-native';

export default function FieldListScreen({ navigation, route }) {
  const [fields, setFields] = useState([]);
  const sport = route.params?.sport;
  const city = route.params?.city;

  const loadFields = () => {
    const params = new URLSearchParams();
    if (sport) params.append('sport', sport);
    if (city) params.append('city', city);
    fetch(`http://localhost:8000/api/fields?${params.toString()}`)
      .then((res) => res.json())
      .then((json) => setFields(json.data || []))
      .catch((err) => console.log(err));
  };

  useEffect(() => {
    loadFields();
  }, [sport, city]);

  useLayoutEffect(() => {
    navigation.setOptions({
      headerRight: () => (
        <Button
          title="Filtros"
          onPress={() => navigation.navigate('Filters', { sport, city })}
        />
      ),
    });
  }, [navigation, sport, city]);

  return (
    <View style={{ flex: 1, paddingTop: 20, paddingHorizontal: 16 }}>
      <FlatList
        data={fields}
        keyExtractor={(item) => item.id.toString()}
        renderItem={({ item }) => (
          <TouchableOpacity onPress={() => navigation.navigate('FieldDetail', { id: item.id })}>
            <Text>
              {item.name} - {item.sport} - Promedio: {item.average_rating ? item.average_rating.toFixed(1) : 'N/A'}
            </Text>
          </TouchableOpacity>
        )}
        ListEmptyComponent={<Text>No hay canchas</Text>}
      />
    </View>
  );
}
