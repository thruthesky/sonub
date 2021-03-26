import 'package:firelamp/firelamp.dart';
import 'package:flutter/material.dart';
import 'package:sonub/widgets/end_drawer.dart';

class HomeScreen extends StatefulWidget {
  @override
  _HomeScreenState createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  String version = '';
  @override
  void initState() {
    super.initState();
    init();
  }

  init() async {
    try {
      version = (await Api.instance.version())['version'];
      setState(() {});
    } catch (e) {
      print('Api error: $e');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
        appBar: AppBar(
          title: Text('Home'),
        ),
        endDrawer: EndDrawer(),
        body: Center(child: Text('Firelamp: version: $version')));
  }
}
