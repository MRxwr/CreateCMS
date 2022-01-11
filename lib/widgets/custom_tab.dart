import 'package:cms/provider/main_provider.dart';
import 'package:cms/utilities/constants.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import 'custom_text.dart';
class CustomTab extends StatelessWidget {
   CustomTab({
     this.icon,
     this.title,
     this.iconHight,
     Key? key}):super(key: key);
   String? icon;
   String? title;
   double? iconHight;
  @override
  Widget build(BuildContext context) {
    return Column(
      mainAxisAlignment: MainAxisAlignment.spaceEvenly,
      crossAxisAlignment: CrossAxisAlignment.center,
      children: [
        //icon
        Image.asset(
          icon!,scale: iconHight,
        ),

        //
        CustomText(
          title: title,
          fontSize: 12,
          color: greyColor,
        )
      ],
    );
  }
}
