# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|
  config.vm.box = "es/centos-7.2"
  config.vm.network "private_network", ip: "192.168.33.20", auto_correct: true
  config.vm.synced_folder ".", "/home/vagrant"
end
