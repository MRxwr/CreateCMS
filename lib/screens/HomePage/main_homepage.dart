import 'package:cms/provider/main_provider.dart';
import 'package:cms/utilities/constants.dart';
import 'package:cms/widgets/custom_inkwell_btn.dart';
import 'package:cms/widgets/custom_parent_widget.dart';
import 'package:cms/widgets/custom_tab.dart';
import 'package:cms/widgets/custom_text.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import 'home_screen.dart';
class MainHomePage extends StatefulWidget {
  const MainHomePage({Key? key}) : super(key: key);

  @override
  _MainHomePageState createState() => _MainHomePageState();
}

class _MainHomePageState extends State<MainHomePage>
    with SingleTickerProviderStateMixin{
  @override
  void initState() {
    super.initState();
    Provider.of<MainProvider>(context,listen:false).tabFuncation(this);
  }
  @override
  Widget build(BuildContext context) {
    return CustomParentWidget(
      child: Scaffold(
        body: Column(
          children: [

            //
            Expanded(
              child: Container(
                child: TabBarView(
                    controller: Provider.of<MainProvider>(context).controller,
                    children: [
                      HomeScreen(),
                      Text("Hello"),
                      Text("Hello"),
                      Text("Hello"),
                    ]
                ),
              ),
            )
          ],
        ),
        bottomNavigationBar: BottomAppBar(
          elevation: 3,
          child: Container(
            height: 60,
            width: double.infinity,
            color: scaffoldBackgroundColor,
            child: TabBar(
                controller: Provider.of<MainProvider>(context).controller,
                indicatorWeight: 0.1,
                tabs: [
                  Tab(child: CustomTab(icon: "assets/icons/ic_home.png",title: "Home",iconHight: 3,)),
                  Tab(child: CustomTab(icon: "assets/icons/ic_checklist.png",title: "Tools",iconHight: 3,)),
                  Tab(child: CustomTab(icon: "assets/icons/ic_add.png",title: "Add",iconHight: 3.9,)),
                  Tab(child: CustomTab(icon: "assets/icons/ic_chat.png",title: "Chats",iconHight: 3,)),
                ]
            ),
          ),
        ),
      ),
    );
  }
}
