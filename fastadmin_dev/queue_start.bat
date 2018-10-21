
@echo off  
rem 进入当前盘符
%~d0 
rem 进入当前所在路径
echo begin to run the following command 
echo php think queue:work --queue receiveOrderQueue --daemon
php think queue:work --queue receiveOrderQueue --daemon


pause

