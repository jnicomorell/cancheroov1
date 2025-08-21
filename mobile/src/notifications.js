import * as Notifications from 'expo-notifications';

export async function registerForPushNotificationsAsync(authToken) {
  const { status: existingStatus } = await Notifications.getPermissionsAsync();
  let finalStatus = existingStatus;
  if (existingStatus !== 'granted') {
    const { status } = await Notifications.requestPermissionsAsync();
    finalStatus = status;
  }
  if (finalStatus !== 'granted') {
    return;
  }

  const tokenData = await Notifications.getDevicePushTokenAsync();
  const fcmToken = tokenData.data;

  await fetch('http://localhost:8000/api/fcm-token', {
    method: 'POST',
    headers: {
      Authorization: `Bearer ${authToken}`,
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ fcm_token: fcmToken }),
  });

  return fcmToken;
}
