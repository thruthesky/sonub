import 'package:firelamp/firelamp.dart';
import 'package:flutter/material.dart';
import 'package:firelamp/widgets/forum/post/post_list.dart';

class PostListScreen extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Post list'),
      ),
      body: PostList(
        categoryId: 'qna',
        builder: (c, ApiPost post) {
          return ListTile(
            title: Text('${post.title}'),
          );
        },
      ),
    );
  }
}
