#!/bin/bash

mkdir -p root/assets/css
mkdir -p root/assets/js
mkdir -p root/assets/images

mkdir -p root/includes
touch root/includes/header.php
touch root/includes/footer.php
touch root/includes/sidebar.php
touch root/includes/db.php

mkdir -p root/modules/auth
mkdir -p root/modules/user
mkdir -p root/modules/product
mkdir -p root/modules/order
mkdir -p root/modules/cart
mkdir -p root/modules/wishlist

mkdir -p root/uploads/products
mkdir -p root/uploads/profiles

mkdir -p root/admin
touch root/admin/dashboard.php
touch root/admin/members.php
touch root/admin/products.php
touch root/admin/orders.php
touch root/admin/categories.php

mkdir -p root/member
touch root/member/dashboard.php
touch root/member/orders.php
touch root/member/wishlist.php
touch root/member/reviews.php

mkdir -p root/public
touch root/public/index.php
touch root/public/products.php
touch root/public/product_detail.php
touch root/public/categories.php
touch root/public/register.php
touch root/public/login.php

mkdir -p root/api
touch root/api/products.php
touch root/api/reviews.php
touch root/api/cart.php

mkdir -p root/docs

touch root/index.php