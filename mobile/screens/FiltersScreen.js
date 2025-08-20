import React, { useState } from 'react';
import { View, TextInput, Button } from 'react-native';
import { useSettings } from '../src/context/SettingsContext';

export default function FiltersScreen({ navigation, route }) {
  const { t } = useSettings();
  const [sport, setSport] = useState(route.params?.sport || '');
  const [city, setCity] = useState(route.params?.city || '');

  return (
    <View style={{ flex: 1, padding: 16 }}>
      <TextInput
        placeholder={t('sport_placeholder')}
        value={sport}
        onChangeText={setSport}
        style={{ borderWidth: 1, marginBottom: 12, padding: 8 }}
      />
      <TextInput
        placeholder={t('city_placeholder')}
        value={city}
        onChangeText={setCity}
        style={{ borderWidth: 1, marginBottom: 12, padding: 8 }}
      />
      <Button
        title={t('apply')}
        onPress={() => navigation.navigate({ name: 'Fields', params: { sport, city }, merge: true })}
      />
    </View>
  );
}
