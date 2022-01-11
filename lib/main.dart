import 'package:cms/provider/main_provider.dart';
import 'package:cms/screens/splash/splash_screen.dart';
import 'package:cms/utilities/constants.dart';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return ChangeNotifierProvider<MainProvider>(
        create: (context) => MainProvider(),
        builder: (context, child) {
          return MaterialApp(
            debugShowCheckedModeBanner: false,
            title: 'Create CMS',
            theme: ThemeData(
                primaryColor: kPrimaryColor,
                textTheme:
                    GoogleFonts.robotoTextTheme(Theme.of(context).textTheme)),
            home: const SplashScreen(),
          );
        });
  }
}
