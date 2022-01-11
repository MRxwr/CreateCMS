import 'dart:async';

import 'package:cms/screens/Account/login_screen.dart';
import 'package:cms/screens/Account/signup_screen.dart';
import 'package:cms/utilities/helper.dart';
import 'package:cms/widgets/custom_parent_widget.dart';

import 'package:flutter/material.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({Key? key}) : super(key: key);

  @override
  _SplashScreenState createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> {
  @override
  void initState() {
    super.initState();
    Timer(const Duration(seconds: 3), () {
      Helper.toScreen(context,  SignUpScreen());
    });
  }

  @override
  Widget build(BuildContext context) {
    return CustomParentWidget(
      child: Scaffold(
        body:  Center(
          child: Container(
            child: Text(
              "Create App",
              style: TextStyle(fontSize: 40),
            ),
          ),
        ),
      ),
    );
  }
}
