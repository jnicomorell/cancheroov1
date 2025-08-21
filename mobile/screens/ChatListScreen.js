import React, { useEffect, useState } from 'react';
import { View, Text, FlatList } from 'react-native';

export default function ChatListScreen() {
  const [chats, setChats] = useState([]);

  useEffect(() => {
    fetch('http://localhost:8000/api/chats')
      .then((res) => res.json())
      .then(setChats)
      .catch((err) => console.log(err));
  }, []);

  return (
    <View style={{ flex: 1, padding: 16 }}>
      <FlatList
        data={chats}
        keyExtractor={(item) => item.id.toString()}
        renderItem={({ item }) => (
          <Text style={{ paddingVertical: 8 }}>{item.name || `Chat ${item.id}`}</Text>
        )}
        ListEmptyComponent={<Text>No hay chats</Text>}
      />
    </View>
  );
}
