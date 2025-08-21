import React, { useEffect, useState } from 'react';
import { View, ActivityIndicator } from 'react-native';
import MapView, { Marker } from 'react-native-maps';

export default function FieldMapScreen({ route }) {
  const [fields, setFields] = useState([]);
  const sport = route.params?.sport;
  const city = route.params?.city;

  useEffect(() => {
    const params = new URLSearchParams();
    if (sport) params.append('sport', sport);
    if (city) params.append('city', city);
    fetch(`http://localhost:8000/api/fields/map?${params.toString()}`)
      .then((res) => res.json())
      .then(setFields)
      .catch(() => setFields([]));
  }, [sport, city]);

  if (fields.length === 0) {
    return (
      <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
        <ActivityIndicator />
      </View>
    );
  }

  const initial = fields[0];
  const initialRegion = {
    latitude: initial.latitude,
    longitude: initial.longitude,
    latitudeDelta: 0.1,
    longitudeDelta: 0.1,
  };

  return (
    <MapView style={{ flex: 1 }} initialRegion={initialRegion}>
      {fields.map((field) => (
        <Marker
          key={field.id}
          coordinate={{ latitude: field.latitude, longitude: field.longitude }}
          title={field.name}
        />
      ))}
    </MapView>
  );
}
