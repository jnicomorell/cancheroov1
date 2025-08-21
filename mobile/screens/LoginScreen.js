import React, { useContext, useState } from 'react';
import { View, Text, TextInput, Button, Linking } from 'react-native';
import { AuthContext } from '../context/AuthContext';
import { useSettings } from '../src/context/SettingsContext';

export default function LoginScreen({ navigation }) {
  const { login } = useContext(AuthContext);
  const { t, language } = useSettings();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');

  const handleLogin = () => {
    fetch(`http://localhost:8000/api/login?lang=${language}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email, password }),
    })
      .then((res) => res.json())
      .then((json) => {
        if (json.token) {
          login(json.token);
        } else {
          setError(t('invalid_credentials'));
        }
      })
      .catch(() => setError(t('connection_error')));
  };

  return (
    <View style={{ flex: 1, justifyContent: 'center', padding: 16 }}>
      <Text style={{ fontSize: 24, marginBottom: 16 }}>{t('login_title')}</Text>
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
      <Button title={t('login_button')} onPress={handleLogin} />
      <View style={{ marginTop: 16 }}>
        <Button title="Ingresar con Google" onPress={() => Linking.openURL('http://localhost:8000/api/auth/google/redirect')} />
      </View>
      <View style={{ marginTop: 16 }}>
        <Button title="Ingresar con Facebook" onPress={() => Linking.openURL('http://localhost:8000/api/auth/facebook/redirect')} />
      </View>
      <View style={{ marginTop: 16 }}>
        <Button title="Ingresar con Apple" onPress={() => Linking.openURL('http://localhost:8000/api/auth/apple/redirect')} />
      </View>
      <View style={{ marginTop: 16 }}>
        <Button
          title={t('create_account')}
          onPress={() => navigation.navigate('Register')}
        />
      </View>
    </View>
  );
}
