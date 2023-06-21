# **FAQ** 常见问题模块

该模块提供了基础的 FAQ 分类，以及 FAQ 的 CRUD。

## 模块安装

```bash
composer require jobsys/faq-module
```

### 依赖

- PHP 依赖 （无）

- JS 依赖 （无）

### 配置

#### 模块配置

```php
"Faq" => [
    "route_prefix" => "manager",                                                    // 路由前缀
]
```

## 模块功能

### FAQ 功能

略

#### 开发规范

只是一些简单的 CRUD，没有什么特别的开发规范。

## 模块代码

### 数据表

```bash
2014_10_12_000007_create_faq_tables                     # FAQ 数据表
```

### 数据模型/Scope

```bash
Modules\Faq\Entities\FaqGroup                # FAQ 分类
Modules\Faq\Entities\Faq                     # FAQ 问题
```


### Controller

```bash
Modules\Faq\Http\Controllers\FaqController        # CRUD API
```

### UI

#### PC 端页面

```bash
web/PageFaq.vue                        # FAQ 管理页面
```
