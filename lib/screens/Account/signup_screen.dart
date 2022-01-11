import 'package:cms/provider/main_provider.dart';
import 'package:cms/screens/HomePage/main_homepage.dart';
import 'package:cms/utilities/constants.dart';
import 'package:cms/utilities/helper.dart';
import 'package:cms/widgets/custom_button.dart';
import 'package:cms/widgets/custom_inkwell_btn.dart';
import 'package:cms/widgets/custom_parent_widget.dart';
import 'package:cms/widgets/custom_text.dart';
import 'package:cms/widgets/custom_textfield.dart';
import 'package:country_code_picker/country_code_picker.dart';
import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import 'login_screen.dart';
class SignUpScreen extends StatelessWidget {
   SignUpScreen({Key? key}) : super(key: key);
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
                            //space
                            const SizedBox(
                              height: 30,
                            ),
                            //Register
                            CustomText(
                              title: "Register",
                              color: kBlackColor,
                              fontSize: 23,
                              fontWeight: FontWeight.w600,
                            ),
                            //space
                            const SizedBox(height: 30,),
                            CustomText(
                              title: "Email",
                              color: kBlackColor,
                              fontSize: 15,
                              fontWeight: FontWeight.w500,
                            ),
                            //space
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
                            //space
                            const SizedBox(
                              height: 10,
                            ),
                            CustomText(
                              title: "Mobile",
                              color: kBlackColor,
                              fontSize: 15,
                              fontWeight: FontWeight.w500,
                            ),
                            Container(
                              child: Row(
                                children: [
                                  Container(
                                    height: 50,
                                    child: Row(
                                      children: [
                                        Icon(Icons.arrow_drop_down),
                                        //
                                        CountryCodePicker(
                                          initialSelection: 'KW',
                                          favorite: ['+965','KW'],
                                          showCountryOnly: false,
                                          onInit: (CountryCode){
                                            // phoneCode = CountryCode!.dialCode;
                                            // print(phoneCode);
                                          },
                                          onChanged: (CountryCode){
                                            // setState(() {
                                            //   phoneCode = CountryCode.dialCode;
                                            // });
                                          },
                                          showFlag: true,
                                          showFlagDialog: true,
                                          showOnlyCountryWhenClosed: false,
                                          alignLeft: false,
                                          padding: EdgeInsets.zero,
                                        ),
                                      ],
                                    ),
                                  ),
                                  //Space
                                  const SizedBox(width: 8,),
                                  Expanded(
                                    child: CustomTextField(
                                      //controller: phoneController,
                                    ),
                                  ),
                                ],
                              ),
                            ),
                            //space
                            const SizedBox(
                              height: 10,
                            ),
                            CustomText(
                              title: "Password",
                              color: kBlackColor,
                              fontSize: 15,
                              fontWeight: FontWeight.w500,
                            ),
                            //space
                            const SizedBox(
                              height: 5,
                            ),
                            CustomTextField(
                              suffixIcon: IconButton(
                                  onPressed: () {
                                    // Provider.of<MainProvider>(context,
                                    //         listen: false)
                                    //     .toggleDone();
                                  },
                                  icon: Provider.of<MainProvider>(context)
                                      .isToggle ==
                                      false
                                      ? Icon(Icons.visibility)
                                      : Icon(Icons.visibility_off)),
                              obscureText:
                              Provider.of<MainProvider>(context).isToggle ==
                                  false,
                              prefixIcon: Icon(Icons.lock),
                              onChanged: (val) {
                                //UserModel().pass = val;
                              },
                              validation: (val) =>
                              val!.isEmpty ? "This field is required" : null,
                            ),
                            //space
                            const SizedBox(
                              height: 10,
                            ),
                            CustomText(
                              title: "Confirm Password",
                              color: kBlackColor,
                              fontSize: 15,
                              fontWeight: FontWeight.w500,
                            ),
                            //space
                            const SizedBox(
                              height: 5,
                            ),
                            CustomTextField(
                              suffixIcon: IconButton(
                                  onPressed: () {
                                    // Provider.of<MainProvider>(context,
                                    //         listen: false)
                                    //     .toggleDone();
                                  },
                                  icon: Provider.of<MainProvider>(context)
                                      .isToggle ==
                                      false
                                      ? Icon(Icons.visibility)
                                      : Icon(Icons.visibility_off)),
                              obscureText:
                              Provider.of<MainProvider>(context).isToggle ==
                                  false,
                              prefixIcon: Icon(Icons.lock),
                              onChanged: (val) {
                                //UserModel().pass = val;
                              },
                              validation: (val) =>
                              val!.isEmpty ? "This field is required" : null,
                            ),
                            //space
                            const SizedBox(
                              height: 20,
                            ),
                            Container(
                              alignment: Alignment.center,
                              child: CustomButton(
                                onPressed: () async {
                                 Helper.toScreen(context, MainHomePage());
                                },
                                btnHeight: 48,
                                btnWidth: 220,
                                btnRadius: 3,
                                title: "Sign Up",
                                btnColor: kPrimaryColor,
                                textColor: kWhiteColor,
                                fontSize: 16,
                              ),
                            ),
                            //space
                            const SizedBox(
                              height: 12,
                            ),
                            Center(
                                child: CustomInkWell(
                                  onTap: (){
                                    Helper.toScreen(context, LoginScreen());
                                  },
                                  child: RichText(
                                    text: TextSpan(
                                        text: "Already have an account? ",
                                        style: TextStyle(color: kBlackColor),
                                        children: [
                                          TextSpan(
                                              recognizer: TapGestureRecognizer()
                                                ..onTap = () {
                                                  Helper.toScreen(context, LoginScreen());
                                                },
                                              text: "Login",
                                              style: const TextStyle(
                                                color: kPrimaryColor,
                                                fontWeight: FontWeight.w500,
                                              ))
                                        ]),
                                  ),
                                )),
                            //space
                            const SizedBox(
                              height: 20,
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
