import 'package:cms/utilities/constants.dart';
import 'package:cms/utilities/helper.dart';
import 'package:cms/widgets/custom_button.dart';
import 'package:cms/widgets/custom_parent_widget.dart';
import 'package:cms/widgets/custom_text.dart';
import 'package:cms/widgets/custom_textfield.dart';
import 'package:flutter/material.dart';
class ForgotPasswordScreen extends StatelessWidget {
   ForgotPasswordScreen({Key? key}) : super(key: key);
   final _formKey = GlobalKey<FormState>();
  @override
  Widget build(BuildContext context) {
    return CustomParentWidget(
      child: Scaffold(
        backgroundColor: scaffoldBackgroundColor,
        body:  Container(
          child: Stack(
            children: [
              Container(
                margin: EdgeInsets.only(bottom: 30),
                alignment: Alignment.bottomCenter,
                child: RichText(
                  textAlign: TextAlign.center,
                  text: const TextSpan(children: [
                    TextSpan(
                        style: TextStyle(color: kBlackColor),
                        text: "Powered By"),
                    TextSpan(
                        style: TextStyle(color: kPrimaryColor),
                        text: "\nCreate"),
                  ]),
                ),
              ),
              Form(
                key: _formKey,
                child: Center(
                  child: SingleChildScrollView(
                    child: Container(
                      padding: EdgeInsets.all(20),
                      width: Helper.setWidth(context, width: 0.9),
                      decoration: BoxDecoration(
                          boxShadow: const [
                            BoxShadow(
                                color: kDarkGrayColor,
                                offset: Offset(0.2, 0.2),
                                blurRadius: 3)
                          ],
                          borderRadius: BorderRadius.circular(20),
                          color: Colors.white),
                      child: ClipRRect(
                        child: Column(
                          mainAxisSize: MainAxisSize.min,
                          mainAxisAlignment: MainAxisAlignment.center,
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Center(
                              child: Image.asset(
                                "assets/icons/main-logo.png",
                                fit: BoxFit.fill,
                              ),
                            ),
                            const SizedBox(
                              height: 30,
                            ),
                            CustomText(
                              title: "Forget Password",
                              color: kBlackColor,
                              fontSize: 23,
                              fontWeight: FontWeight.w600,
                            ),
                            const SizedBox(
                              height: 30,
                            ),
                            CustomText(
                              title: "Email",
                              color: kBlackColor,
                              fontSize: 15,
                              fontWeight: FontWeight.w500,
                            ),
                            const SizedBox(
                              height: 5,
                            ),
                            CustomTextField(
                              validation: (val) {
                                Pattern pattern =
                                    r"^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]"
                                    r"{0,253}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]"
                                    r"{0,253}[a-zA-Z0-9])?)*$";
                                RegExp regex = new RegExp(pattern.toString());
                                if (!regex.hasMatch(val!) || val == null)
                                  return 'Enter a valid email address';
                                else
                                  return null;
                              },
                              onChanged: (val) {
                                // UserModel().email = val;
                              },
                              prefixIcon: Icon(Icons.mail),
                            ),
                            const SizedBox(
                              height: 20,
                            ),
                            Container(
                              alignment: Alignment.center,
                              child: CustomButton(
                                onPressed: () async {

                                },
                                btnHeight: 48,
                                btnWidth: 220,
                                btnRadius: 3,
                                title: "Recover",
                                btnColor: kPrimaryColor,
                                textColor: kWhiteColor,
                                fontSize: 16,
                              ),
                            ),
                            const SizedBox(
                              height: 12,
                            ),
                          ],
                        ),
                      ),
                    ),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),);
  }
}
