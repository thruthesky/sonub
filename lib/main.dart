import 'package:firelamp/firelamp.dart';
import 'package:flutter/material.dart';
import 'package:sonub/screens/about/about.screen.dart';
import 'package:sonub/screens/forum/post.list.screen.dart';
import 'package:sonub/screens/home/home.screen.dart';
import 'package:get/get.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();
  runApp(MyApp());
}

class MyApp extends StatelessWidget {
  MyApp() {
    Api.instance.init(apiUrl: 'https://itsuda50.com/index.php');
  }
  @override
  Widget build(BuildContext context) {
    return GetMaterialApp(
      home: HomeScreen(),
      getPages: [
        GetPage(name: 'home', page: () => HomeScreen()),
        GetPage(name: 'about', page: () => AboutScreen()),
        GetPage(name: 'postList', page: () => PostListScreen()),
      ],
    );
  }
}
