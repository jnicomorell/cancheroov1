import React, { useContext, useEffect } from 'react';
import { View, Text } from 'react-native';
import { NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { AuthProvider, AuthContext } from './context/AuthContext';
import { registerForPushNotificationsAsync } from './src/notifications';
import LoginScreen from './screens/LoginScreen';
import RegisterScreen from './screens/RegisterScreen';
import FieldListScreen from './screens/FieldListScreen';
import FieldDetailScreen from './screens/FieldDetailScreen';
import ReservationsScreen from './screens/ReservationsScreen';
import FiltersScreen from './screens/FiltersScreen';
import { SettingsProvider, useSettings } from './src/context/SettingsContext';

const RootStack = createNativeStackNavigator();
const FieldsStack = createNativeStackNavigator();
const ReservationsStack = createNativeStackNavigator();
const Tab = createBottomTabNavigator();

function FieldsStackScreen() {
  const { t } = useSettings();
  return (
    <FieldsStack.Navigator>
      <FieldsStack.Screen name="Fields" component={FieldListScreen} options={{ title: t('fields') }} />
      <FieldsStack.Screen name="FieldDetail" component={FieldDetailScreen} options={{ title: t('detail') }} />
      <FieldsStack.Screen name="Filters" component={FiltersScreen} options={{ title: t('filters') }} />
    </FieldsStack.Navigator>
  );
}

function ReservationsStackScreen() {
  const { t } = useSettings();
  return (
    <ReservationsStack.Navigator>
      <ReservationsStack.Screen name="Reservations" component={ReservationsScreen} options={{ title: t('reservations') }} />
    </ReservationsStack.Navigator>
  );
}

function AppTabs() {
  const { t } = useSettings();
  return (
    <Tab.Navigator>
      <Tab.Screen name="Canchas" component={FieldsStackScreen} options={{ title: t('fields'), headerShown: false }} />
      <Tab.Screen name="Mis reservas" component={ReservationsStackScreen} options={{ title: t('my_reservations'), headerShown: false }} />
    </Tab.Navigator>
  );
}

function RootNavigator() {
  const { token, loading } = useContext(AuthContext);
  const { t } = useSettings();
  if (loading) {
    return (
      <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
        <Text>{t('loading')}</Text>
      </View>
    );
  }
  return (
    <RootStack.Navigator screenOptions={{ headerShown: false }}>
      {token ? (
        <RootStack.Screen name="App" component={AppTabs} />
      ) : (
        <>
          <RootStack.Screen name="Login" component={LoginScreen} />
          <RootStack.Screen name="Register" component={RegisterScreen} />
        </>
      )}
    </RootStack.Navigator>
  );
}

export default function App() {
  return (
    <SettingsProvider>
      <AuthProvider>
        <NavigationContainer>
          <RootNavigator />
        </NavigationContainer>
      </AuthProvider>
    </SettingsProvider>
  );
}
