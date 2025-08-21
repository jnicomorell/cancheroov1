import React, { useContext, useState } from 'react';
import { View, Text, TextInput, Button } from 'react-native';
import { AuthContext } from '../context/AuthContext';
import { useSettings } from '../src/context/SettingsContext';

export default function RegisterScreen({ navigation }) {
  const { login } = useContext(AuthContext);
  const { t, language } = useSettings();
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');

  const handleRegister = () => {
    fetch(`http://localhost:8000/api/register?lang=${language}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ name, email, password }),
    })
      .then((res) => res.json())
      .then((json) => {
        if (json.token) {
          login(json.token);
        } else {
          setError(t('registration_error'));
        }
      })
      .catch(() => setError(t('connection_error')));
  };

  return (
    <View style={{ flex: 1, justifyContent: 'center', padding: 16 }}>
      <Text style={{ fontSize: 24, marginBottom: 16 }}>{t('register_title')}</Text>
      <TextInput
        placeholder={t('name')}
        value={name}
        onChangeText={setName}
        style={{ borderWidth: 1, marginBottom: 12, padding: 8 }}
      />
      <TextInput
        placeholder={t('email')}
        value={email}
        onChangeText={setEmail}
        autoCapitalize="none"
        keyboardType="email-address"
        style={{ borderWidth: 1, marginBottom: 12, padding: 8 }}
      />
      <TextInput
        placeholder={t('password')}
        value={password}
        onChangeText={setPassword}
        secureTextEntry
        style={{ borderWidth: 1, marginBottom: 12, padding: 8 }}
      />
      {error ? <Text style={{ color: 'red' }}>{error}</Text> : null}
      <Button title={t('register_button')} onPress={handleRegister} />
      <View style={{ marginTop: 16 }}>
        <Button title={t('back')} onPress={() => navigation.goBack()} />
      </View>
    </View>
  );
}
