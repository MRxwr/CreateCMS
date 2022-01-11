class CustomModel{
  String? title;
  String? subTitle;
  String? image;
  CustomModel({this.title,this.subTitle,this.image});
}

//
List<CustomModel>  homeScreenList =[
  CustomModel(image: "assets/icons/ic_manager.png",title: "Admins",subTitle: "10"),
  CustomModel(image: "assets/icons/ic_employee.png",title: "Managers",subTitle: "10"),
  CustomModel(image: "assets/icons/ic_clipboard.png",title: "On going Projects",subTitle: "100"),
  CustomModel(image: "assets/icons/ic_task.png",title: "Cancelled Projects",subTitle: "10"),
  CustomModel(image: "assets/icons/ic_clipboard.png",title: "Panding Projects",subTitle: "10"),
  CustomModel(image: "assets/icons/ic_task.png",title: "Completed Projects",subTitle: "10"),
];