作者：日⺌兀
规则：
    ① Application 目录下除了Common,Controller,View,Model不被认为是模块
    ② 所有继承Soya的类加载时均自动进行初始化，并且所有方法改为静态方法，避免new一个对象进行的消耗