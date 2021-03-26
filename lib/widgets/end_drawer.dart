import 'package:flutter/material.dart';
import 'package:get/get.dart';

class EndDrawer extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Drawer(
      // Add a ListView to the drawer. This ensures the user can scroll
      // through the options in the drawer if there isn't enough vertical
      // space to fit everything.
      child: ListView(
        // Important: Remove any padding from the ListView.
        padding: EdgeInsets.zero,
        children: <Widget>[
          DrawerHeader(
            child: Text('Drawer Header'),
            decoration: BoxDecoration(
              color: Colors.blue,
            ),
          ),
          ListTile(
            title: Text('Home'),
            onTap: () => Get.toNamed('home'),
          ),
          ListTile(
            title: Text('About'),
            onTap: () => Get.toNamed('about'),
          ),
          ListTile(
            title: Text('QnA'),
            onTap: () =>
                Get.toNamed('postList', arguments: {'categoryId': 'qna'}),
          ),
        ],
      ),
    );
  }
}
