import 'package:cms/utilities/helper.dart';
import 'package:flutter/material.dart';
import 'custom_button.dart';
import 'custom_text.dart';

class CustomDialogue extends StatelessWidget {

  CustomDialogue({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Dialog(
      insetPadding: EdgeInsets.symmetric(horizontal: 30),
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
      clipBehavior: Clip.hardEdge,
      child: Container(
        height: 230,
        child: Column(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
          //
            Container(
              height: 55,
              width: double.infinity,
              color: Colors.greenAccent,
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  //Space
                  SizedBox(width: 20,),
                  CustomText(
                    title: "Confirmar",
                    fontSize: 17,
                    color: Colors.greenAccent,
                  ),
                  //
                  IconButton(
                      onPressed: (){
                        Navigator.pop(context);
                      },
                      icon: Icon(Icons.clear,size: 22,))
                ],
              ),
            ),

            //
            Container(
              alignment: Alignment.center,
              child: RichText(
                  text: const TextSpan(
                      text: "você gostaria de se desconectar? ",
                      style: TextStyle(
                          fontSize: 14,
                          color: Colors.greenAccent
                      ),
                      children: [
                        TextSpan(
                          text: "Alex",
                          style: TextStyle(
                              fontSize: 14,
                              color: Colors.greenAccent,
                              fontWeight: FontWeight.w700
                          ),),
                      ]
                  )
              ),
            ),
            Container(
              padding: EdgeInsets.only(left: 14,right: 14,bottom: 12),
              child: Row(
                children: [
                  Expanded(
                      child:  CustomButton(
                        onPressed: () {
                          //     Helper.toRemoveUntiScreen(context, LogInScreen());
                        },
                        btnHeight: 48,
                        btnRadius: 6,
                        title: "Sim",
                        btnBorderColor: Colors.greenAccent,
                        textColor: Colors.greenAccent,
                        fontSize: 16,
                      )),
                  //Space
                  SizedBox(width: 15,),
                  Expanded(
                      child:  CustomButton(
                        onPressed: () {
                         Navigator.pop(context);
                        },
                        btnHeight: 48,
                        btnRadius: 6,
                        title: "Não",
                        btnColor: Colors.greenAccent,
                        textColor: Colors.greenAccent,
                        fontSize: 16,
                      )),
                ],
              ),
            )
          ],
        ),
      ),
    );
  }
}
