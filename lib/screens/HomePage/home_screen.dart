import 'package:cms/models/custom_model.dart';
import 'package:cms/utilities/constants.dart';
import 'package:cms/widgets/custom_text.dart';
import 'package:flutter/material.dart';
import 'package:percent_indicator/percent_indicator.dart';
class HomeScreen extends StatelessWidget {
  const HomeScreen({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Stack(
      children: [
        //Appbar
        Container(
          width: double.infinity,
          height: 150,
          color: kPrimaryColor,
          padding: EdgeInsets.only(left: 18,right: 18,top: 24),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.center,
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Expanded(
                  child: Container(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        //
                        CustomText(
                          title: "Welcome Nasir!",
                          fontSize: 21,
                          color: kWhiteColor,
                          fontWeight: FontWeight.w700,
                        ),
                        //Space
                        SizedBox(height: 10,),
                        CustomText(
                          title: "Today's Summary",
                          fontSize: 17,
                          color: kWhiteColor,
                        ),
                      ],
                    ),
                  )
              ),
              Container(
                width: 80,
                height: 80,
                decoration: const BoxDecoration(
                    shape: BoxShape.circle,
                    image: DecorationImage(
                        fit: BoxFit.cover,
                        image: AssetImage("assets/images/profile_img.png")
                    )
                ),
              ),
            ],
          ),
        ),
        Positioned.directional(
          textDirection: Directionality.of(context),
          top: 130,
          bottom: 0,
          start: 0,
          end: 0,
          child: Container(
            width: double.infinity,
            height: double.infinity,
            decoration: const BoxDecoration(
              color: kWhiteColor,
              borderRadius: BorderRadius.only(
                topLeft: Radius.circular(25),
                topRight: Radius.circular(25)
              ),
            ),
            child: ListView(
              children:  [
              //Space
                const SizedBox(
                  height: 10,
                ),
                //
                Container(
                  padding: EdgeInsets.symmetric(horizontal: 22),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      customProggressWidget(
                        title: "Over All",
                        pers: 35
                      ),
                      customProggressWidget(
                          title: "Projects",
                          pers: 70
                      ),
                      customProggressWidget(
                          title: "Tasks",
                          pers: 55
                      ),
                    ],
                  ),
                ),
                //Space
                SizedBox(height: 15,),
                customBoxWidget()
              ],
            ),
          ),
        ),
      ],
    );
  }
  Container customProggressWidget({String?title,double? pers}){
    return Container(
      child: Column(
        children: [
          //
          CustomText(
            title: title,
            fontSize: 18,
            color: greyColor,
            fontWeight: FontWeight.w700,
          ),
          //Space
          SizedBox(height: 10,),
          //
          CircularPercentIndicator(
            radius: 80.0,
            lineWidth: 10.0,
            percent: 0.60,
            center: new Text("$pers%"),
            progressColor: kPrimaryColor,

          ),
        ],
      ),
    );
  }

   customBoxWidget(){
  return  GridView.count(
      padding: const EdgeInsets.symmetric(horizontal: 22, vertical: 8),
      physics: const NeverScrollableScrollPhysics(),
      childAspectRatio: 1.4,
      crossAxisCount: 2,
      mainAxisSpacing: 15,
      crossAxisSpacing: 15,
      cacheExtent: 100,
      shrinkWrap: true,
      children: List.generate(homeScreenList.length, (index){
        return Container(
          width: double.infinity,
          height: 100,
          decoration: BoxDecoration(
            color: scaffoldBackgroundColor,
            borderRadius: BorderRadius.circular(25)
          ),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              //
              Image.asset("${homeScreenList[index].image}",scale: 2.4,),
              //Space
              SizedBox(height: 10,),
              //
              CustomText(
                title: homeScreenList[index].title,
                fontSize: 14,
                color: greyColor,
              ),
              //Space
              SizedBox(height: 10,),
              //
              CustomText(
                title: homeScreenList[index].subTitle,
                fontSize: 14,
                color: kPrimaryColor,
              ),
            ],
          ),
        );
      })
    );
  }
}
