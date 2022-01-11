
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
class CustomTextField extends StatelessWidget {
  CustomTextField({
    this.obscureText,
    this.focusNode,
    this.onChanged,
     this.validation,
    this.controller,
    this.keyboardType,
    this.hintText,
    this.isFilled,
    this.isUnderlineInputBorder,
    this.isOutlineInputBorder,
    this.isOutlineInputBorderColor,
    this.maxLength,
    this.fontSize,
    this.inputFormatter,
    this.onTap,
    this.fillColor,
    this.fieldborderColor,
    this.fieldborderRadius,
    this.maxLines,
    this.contentPaddingLeft,
    this.contentPaddingRight,
    this.contentPaddingTop,
    this.contentPaddingBottom,
    this.textColor,
    this.hintTextColor,
    this.hintFontSize,
    this.textFontSize,
    this.textAlign,
    this.prefixIcon,
    this.suffixIcon,
    Key? key}) : super(key: key);
  bool ? obscureText;
  String? Function(String?)? validation;
  Function? onChanged;
  Function? onTap;
  FocusNode? focusNode;
  TextEditingController? controller;
  TextInputType? keyboardType;
  String ? hintText;
  bool? isFilled;
  bool? isUnderlineInputBorder;
  bool? isOutlineInputBorder;
  Color? isOutlineInputBorderColor;
  int? maxLength;
  int? maxLines;
  double? fontSize;
  int? inputFormatter;
  Color? fillColor;
  Color? fieldborderColor;
  Color? textColor;
  Color? hintTextColor;
  double? textFontSize;
  double? hintFontSize;
  double? fieldborderRadius;
  double? contentPaddingTop;
  double? contentPaddingBottom;
  double? contentPaddingLeft;
  double? contentPaddingRight;
  TextAlign? textAlign;
  Widget? prefixIcon;
  Widget? suffixIcon;
  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
          color:fillColor??Colors.transparent,
          border: Border.all(
              width: fieldborderColor!=null? 0:1,
              color: fieldborderColor??Colors.transparent
          ),
          borderRadius: BorderRadius.circular(fieldborderRadius??4)
      ),
      child: TextFormField(
        obscureText: obscureText??false,
        maxLength: maxLength,
        maxLines: maxLines??1,
        focusNode: focusNode,
        onTap: ()=>onTap,
        validator: validation,
        onChanged:(_)=> onChanged,
        controller: controller,
        keyboardType: keyboardType,
        textAlign: textAlign??TextAlign.left,
      //  cursorColor: cursorColor,
        decoration: InputDecoration(
          contentPadding:  EdgeInsets.only(left: contentPaddingLeft??12,right:contentPaddingRight?? 12,
          top: contentPaddingTop??13,bottom: contentPaddingBottom??13),
          isDense: true,
          hintText: hintText,
          hintStyle:  TextStyle(
              fontSize: hintFontSize??15,
              color: hintTextColor
          ),
          helperStyle:  const TextStyle(
              color: Colors.red,
            fontSize: 10
          ),
          errorStyle: const TextStyle(
              color: Colors.red
          ),
          prefixIcon: prefixIcon,
          suffixIcon: suffixIcon,
          filled: true,
          fillColor: isOutlineInputBorderColor,
          border:isUnderlineInputBorder==false||isOutlineInputBorder==false?InputBorder.none:isOutlineInputBorder==false?
          UnderlineInputBorder():OutlineInputBorder(
              borderSide: BorderSide(
                  width: isOutlineInputBorderColor!=null? 0:1,
                  color: isOutlineInputBorderColor??Colors.transparent
              )
          ),
          enabledBorder:isUnderlineInputBorder==false||isOutlineInputBorder==false?InputBorder.none:isOutlineInputBorder==false?
          UnderlineInputBorder():OutlineInputBorder(
              borderSide: BorderSide(
                  width: isOutlineInputBorderColor!=null? 0:1,
                  color: isOutlineInputBorderColor??Colors.transparent
              )
          ),
          focusedBorder:isUnderlineInputBorder==false||isOutlineInputBorder==false?InputBorder.none:isOutlineInputBorder==false?
          UnderlineInputBorder():OutlineInputBorder(
              borderSide: BorderSide(
                  width: isOutlineInputBorderColor!=null? 0:1,
                  color: isOutlineInputBorderColor??Colors.transparent
              ),
          ),
          errorBorder: isUnderlineInputBorder==false||isOutlineInputBorder==false?InputBorder.none:isOutlineInputBorder==false?
          UnderlineInputBorder():OutlineInputBorder(
            borderSide: BorderSide(
                width: isOutlineInputBorderColor!=null? 0:1,
               // color: redColor
            ),
          ),
        ),
        inputFormatters: [LengthLimitingTextInputFormatter(inputFormatter)],
        style:  TextStyle(
          color: textColor??Colors.black,
          fontSize: textFontSize??15,
        ),
      ),
    );
  }
}
