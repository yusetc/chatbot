# -*- mode: ruby -*-
# vi: set ft=ruby :

# All Vagrant configuration is done below. The "2" in Vagrant.configure
# configures the configuration version (we support older styles for
# backwards compatibility). Please don't change it unless you know what
# you're doing.
Vagrant.configure("2") do |config|
  # The most common configuration options are documented and commented below.
  # For a complete reference, please see the online documentation at
  # https://docs.vagrantup.com.
  config.vm.define "apiProject" do |machine_id|
      # Every Vagrant development environment requires a box. You can search for
      # boxes at https://vagrantcloud.com/search.
      machine_id.vm.box = "debian/stretch64"

      # Create a forwarded port mapping which allows access to a specific port
      # within the machine from a port on the host machine. In the example below,
      # accessing "localhost:8080" will access port 80 on the guest machine.
      # NOTE: This will enable public access to the opened port
       machine_id.vm.network "forwarded_port", guest: 80, host: 80
       machine_id.vm.network "forwarded_port", guest: 443, host: 443
       machine_id.vm.network "forwarded_port", guest: 9200, host: 9200
       machine_id.vm.network "forwarded_port", guest: 8080, host: 8080
	   machine_id.vm.network "forwarded_port", guest: 3306, host: 3306

      # Create a private network, which allows host-only access to the machine
      # using a specific IP.
       machine_id.vm.network "private_network", ip: "192.168.33.10"

      # Share an additional folder to the guest VM. The first argument is
      # the path on the host to the actual folder. The second argument is
      # the path on the guest to mount the folder. And the optional third
      # argument is a set of non-required options.
       machine_id.vm.synced_folder ".", "/var/www", type: "smb", smb_username: "" ,smb_password: "", :mount_options => ["mfsymlinks,dir_mode=0777,file_mode=0777,vers=3.0"]
       machine_id.vm.synced_folder ".", "/vagrant", type: "smb", smb_username: "" ,smb_password: "", :mount_options => ["mfsymlinks,dir_mode=0777,file_mode=0777,vers=3.0"]

      # Provider-specific configuration so you can fine-tune various
      # backing providers for Vagrant. These expose provider-specific options.
      # Example for VirtualBox:
      config.vm.provider "virtualbox" do |vb|
         vb.memory = "4096"
         vb.cpus = 4
         vb.name = "apiProject"
         vb.customize ["setextradata", :id, "VBoxInternal2/SharedFoldersEnableSymlinksCreate/v-root", "1"]
      end

      machine_id.vm.provision "shell", inline: <<-SHELL
         apt-get update
         apt -y install apt-transport-https ca-certificates curl gnupg2 software-properties-common apache2-utils vim jq net-tools
         mkdir -p /var/data/mysql || true
         chmod -R 0777 /var/data/mysql
      SHELL

      machine_id.vm.provision "shell", path: "infrastructure/scripts/setup-docker.sh"

      machine_id.vm.provision "shell", :run => 'always', :path => "infrastructure/scripts/run-container.sh"

      machine_id.vm.provision "shell", :run => 'always', :path => "infrastructure/scripts/setup-scripts.sh"

      machine_id.vm.post_up_message = ""
  end
end
