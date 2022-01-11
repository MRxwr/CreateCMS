import 'package:flutter/material.dart';

class MainProvider extends ChangeNotifier {
  bool isLoading = false;
  bool isToggle = false;
  bool isPlaceHolder = false;
  String lang = "en";
  TextDirection textDir = TextDirection.ltr;

  toggleDone() {
    isToggle = !isToggle;
    notifyListeners();
  }

  changeLang(String langString) {
    lang = langString;
    if (langString == "en") {
      textDir = TextDirection.ltr;
    } else {
      textDir = TextDirection.rtl;
    }

    notifyListeners();
  }

  changeTextDirection(String langes) {
    lang = langes;
    if (langes == "en") {
      textDir = TextDirection.ltr;
    } else {
      textDir = TextDirection.rtl;
    }
  }

  changeIsLoading(bool value) {
    isLoading = value;
    notifyListeners();
  }

  changeIsPlaceHolder(bool value) {
    isPlaceHolder = value;
    notifyListeners();
  }

  //
  TabController? controller;
  tabFuncation(TickerProvider vsync){
    controller = TabController(length: 4, vsync: vsync);
    controller!.addListener(tabIndexChange);
    notifyListeners();
  }

  tabIndexChange(){
    notifyListeners();
  }

}
