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
import LoyaltyScreen from './screens/LoyaltyScreen';

const RootStack = createNativeStackNavigator();
const FieldsStack = createNativeStackNavigator();
const ReservationsStack = createNativeStackNavigator();
const Tab = createBottomTabNavigator();

function FieldsStackScreen() {
  return (
    <FieldsStack.Navigator>
      <FieldsStack.Screen name="Fields" component={FieldListScreen} options={{ title: 'Canchas' }} />
      <FieldsStack.Screen name="FieldDetail" component={FieldDetailScreen} options={{ title: 'Detalle' }} />
      <FieldsStack.Screen name="Filters" component={FiltersScreen} options={{ title: 'Filtros' }} />
      <FieldsStack.Screen name="FieldMap" component={FieldMapScreen} options={{ title: 'Mapa' }} />
    </FieldsStack.Navigator>
  );
}

function ReservationsStackScreen() {
  return (
    <ReservationsStack.Navigator>
      <ReservationsStack.Screen name="Reservations" component={ReservationsScreen} options={{ title: 'Reservas' }} />
    </ReservationsStack.Navigator>
  );
}

function AppTabs() {
  return (
    <Tab.Navigator>
      <Tab.Screen name="Canchas" component={FieldsStackScreen} options={{ headerShown: false }} />
      <Tab.Screen name="Mis reservas" component={ReservationsStackScreen} options={{ headerShown: false }} />
      <Tab.Screen name="Promos" component={LoyaltyScreen} />
    </Tab.Navigator>
  );
}

function RootNavigator() {
  const { token, loading } = useContext(AuthContext);

  useEffect(() => {
    if (token) {
      registerForPushNotificationsAsync(token);
    }
  }, [token]);
  if (loading) {
    return (
      <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
        <Text>Cargando...</Text>
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
    <AuthProvider>
      <NavigationContainer>
        <RootNavigator />
      </NavigationContainer>
    </AuthProvider>
  );
}
