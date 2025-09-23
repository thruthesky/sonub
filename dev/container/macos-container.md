

## macOS Container 중요 제한사항 및 해결방법

### 1. 개별 파일 마운트 불가능 (VZErrorDomain Code=2)
- **문제**: macOS Container는 Docker와 달리 개별 파일 마운트를 지원하지 않습니다.
- **에러**: `Error Domain=VZErrorDomain Code=2 "A directory sharing device configuration is invalid."`
- **해결방법**:
  - 파일이 아닌 디렉터리 전체를 마운트해야 합니다.
  - 예: `php.ini` 파일 대신 전체 설정 디렉터리를 마운트
  - PHP-FPM: `/usr/local/etc/php/conf.d/` 디렉터리에 `*.ini` 파일들을 자동 로드
  - Nginx: `/etc/nginx/conf.d/` 디렉터리에 `*.conf` 파일들을 자동 로드

#### 올바른 사용 예시
```bash
# 잘못된 방법 (파일 마운트 - 에러 발생)
-v "${PHP_CONF_DIR}/php.ini:/usr/local/etc/php/php.ini"

# 올바른 방법 (디렉터리 마운트)
-v "${PHP_CONF_DIR}:/usr/local/etc/php/conf.d"
```

### 2. 컨테이너 간 네트워크 통신
- **문제**: macOS Container에서 컨테이너 간 DNS 해결이 항상 작동하지 않을 수 있습니다.
- **해결방법**:
  - 컨테이너 이름 대신 IP 주소를 직접 사용
  - PHP-FPM 컨테이너는 일반적으로 `192.168.65.x` 대역에서 실행됨
  - `container inspect` 명령으로 컨테이너의 실제 IP 주소 확인

#### 네트워크 구성 예시
```bash
# PHP-FPM 컨테이너 시작
container run -d --name php-fpm --network webnet \
  -v /var/www/html:/var/www/html \
  docker.io/php:fpm-alpine

# PHP-FPM IP 주소 확인
container inspect php-fpm | grep -E '"address"'
# 예: "address": "192.168.65.12/24"

# Nginx 설정에서 IP 주소 사용
server {
  location ~ \.php$ {
    fastcgi_pass 192.168.65.12:9000;  # 컨테이너 이름 대신 IP 사용
  }
}
```

### 3. macOS 26 (Tahoe)에서의 작동 확인
- PHP-FPM과 Nginx가 별도의 컨테이너로 실행되며 정상 통신 확인
- 네트워크 격리와 컨테이너 간 통신이 정상 작동
- 포트 매핑: `127.0.0.1:8080` → 컨테이너 포트 80

---

MacOS Container 공식 홈페이지:
https://github.com/apple/container

Tutorial: https://github.com/apple/container/blob/main/docs/tutorial.md
How-to: https://github.com/apple/container/blob/main/docs/how-to.md
Technical Overview: https://github.com/apple/container/blob/main/docs/technical-overview.md
Command Reference: https://github.com/apple/container/blob/main/docs/command-reference.md


container
container is a tool that you can use to create and run Linux containers as lightweight virtual machines on your Mac. It's written in Swift, and optimized for Apple silicon.

The tool consumes and produces OCI-compatible container images, so you can pull and run images from any standard container registry. You can push images that you build to those registries as well, and run the images in any other OCI-compatible application.

container uses the Containerization Swift package for low level container, image, and process management.

introductory movie showing some basic commands

Get started
Requirements
You need a Mac with Apple silicon to run container. To build it, see the BUILDING document.

container is supported on macOS 26, since it takes advantage of new features and enhancements to virtualization and networking in this release. We do not support older versions of macOS and the container maintainers typically will not address issues that cannot be reproduced on the latest macOS 26 beta.

Install or upgrade
If you're upgrading, first stop and uninstall your existing container (the -k flag keeps your user data, while -d removes it):

container system stop
uninstall-container.sh -k
Download the latest signed installer package for container from the GitHub release page.

To install the tool, double-click the package file and follow the instructions. Enter your administrator password when prompted, to give the installer permission to place the installed files under /usr/local.

Start the system service with:

container system start
Uninstall
Use the uninstall-container.sh script to remove container from your system. To remove your user data along with the tool, run:

uninstall-container.sh -d
To retain your user data so that it is available should you reinstall later, run:

uninstall-container.sh -k
Next steps
Take a guided tour of container by building, running, and publishing a simple web server image.
Learn how to use various container features.
Read a brief description and technical overview of container.
Browse the full command reference.
Build and run container on your own development system.
View the project API documentation.
Contributing
Contributions to container are welcomed and encouraged. Please see our main contributing guide for more information.

Project Status
The container project is currently under active development. Its stability, both for consuming the project as a Swift package and the container tool, is only guaranteed within patch versions, such as between 0.1.1 and 0.1.2. Minor version number releases may include breaking changes until we achieve a 1.0.0 release.

Tutorial
Take a guided tour of container by building, running, and publishing a simple web server image.

Try out the container CLI
Start the application, and try out some basic commands to familiarize yourself with the command line interface (CLI) tool.

Start the container service
Start the services that container uses:

container system start
If you have not installed a Linux kernel yet, the command will prompt you to install one:

% container system start

Verifying apiserver is running...
Installing base container filesystem...
No default kernel configured.
Install the recommended default kernel from [https://github.com/kata-containers/kata-containers/releases/download/3.17.0/kata-static-3.17.0-arm64.tar.xz]? [Y/n]: y
Installing kernel...
%
Then, verify that the application is working by running a command to list all containers:

container list --all
If you haven't created any containers yet, the command outputs an empty list:

% container list --all
ID  IMAGE  OS  ARCH  STATE  ADDR
%
Get CLI help
You can get help for any container CLI command by appending the --help option:

% container --help
OVERVIEW: A container platform for macOS

USAGE: container [--debug] 

OPTIONS:
  --debug                 Enable debug output [environment: CONTAINER_DEBUG]
  --version               Show the version.
  -h, --help              Show help information.

CONTAINER SUBCOMMANDS:
  create                  Create a new container
  delete, rm              Delete one or more containers
  exec                    Run a new command in a running container
  inspect                 Display information about one or more containers
  kill                    Kill one or more running containers
  list, ls                List containers
  logs                    Fetch container stdio or boot logs
  run                     Run a container
  start                   Start a container
  stop                    Stop one or more running containers

IMAGE SUBCOMMANDS:
  build                   Build an image from a Dockerfile
  image, i                Manage images
  registry, r             Manage registry configurations

SYSTEM SUBCOMMANDS:
  builder                 Manage an image builder instance
  system, s               Manage system components

%
Abbreviations
You can save keystrokes by abbreviating commands and options. For example, abbreviate the container list command to container ls, and the --all option to -a:

% container ls -a
ID  IMAGE  OS  ARCH  STATE  ADDR
%
Use the --help flag to see which abbreviations exist.

Set up a local DNS domain (optional)
container includes an embedded DNS service that simplifies access to your containerized applications. If you want to configure a local DNS domain named test for this tutorial, run:

sudo container system dns create test
container system property set dns.domain test
Enter your administrator password when prompted. The first command requires administrator privileges to create a file containing the domain configuration under the /etc/resolver directory, and to tell the macOS DNS resolver to reload its configuration files.

The second command makes test the default domain to use when running a container with an unqualified name. For example, if the default domain is test and you use --name my-web-server to start a container, queries to my-web-server.test will respond with that container's IP address.

Build an image
Set up a Dockerfile for a basic Python web server, and use it to build a container image named web-test.

Set up a simple project
Start a terminal, create a directory named web-test for the files needed to create the container image:

mkdir web-test
cd web-test
In the web-test directory, create a file named Dockerfile with this content:

FROM docker.io/python:alpine
WORKDIR /content
RUN apk add curl
RUN echo '<!DOCTYPE html><html><head><title>Hello</title></head><body><h1>Hello, world!</h1></body></html>' > index.html
CMD ["python3", "-m", "http.server", "80", "--bind", "0.0.0.0"]
The FROM line instructs the container builder to start with a base image containing the latest production version of Python 3.

The WORKDIR line creates a directory /content in the image, and makes it the current directory.

The first RUN line adds the curl command to your image, and the second RUN line creates a simple HTML landing page named /content/index.html.

The CMD line configures the container to run a simple web server in Python on port 80. Since the working directory is /content, the web server runs in that directory and delivers the content of the file /content/index.html when a user requests the index page URL.

The server listens on the wildcard address 0.0.0.0 to allow connections from the host and other containers. You can safely use the listen address 0.0.0.0 inside the container, because external systems have no access to the virtual network to which the container attaches.

Build the web server image
Run the container build command to create an image with the name web-test from your Dockerfile:

container build --tag web-test --file Dockerfile .
The last argument . tells the builder to use the current directory (web-test) as the root of the build context. You can copy files within the build context into your image using the COPY command in your Dockerfile.

After the build completes, list the images. You should see both the base image and the image that you built in the results:

% container image list
NAME      TAG     DIGEST
python    alpine  b4d299311845147e7e47c970...
web-test  latest  25b99501f174803e21c58f9c...
%
Run containers
Using your container image, run a web server and try out different ways of interacting with it.

Start the webserver
Use container run to start a container named my-web-server that runs your webserver:

container run --name my-web-server --detach --rm web-test
The --detach flag runs the container in the background, so that you can continue running commands in the same terminal. The --rm flag causes the container to be removed automatically after it stops.

When you list containers now, my-web-server is present, along with the container that container started to build your image. Note that its IP address, shown in the ADDR column, is 192.168.64.3:

% container ls
ID             IMAGE                                               OS     ARCH   STATE    ADDR
buildkit       ghcr.io/apple/container-builder-shim/builder:0.0.3  linux  arm64  running  192.168.64.2
my-web-server  web-test:latest                                     linux  arm64  running  192.168.64.3
%
Open the website, using the container's IP address in the URL:

open http://192.168.64.3
If you configured the local domain test earlier in the tutorial, you can also open the page with the full hostname for the container:

open http://my-web-server.test
Run other commands in the container
You can run other commands in my-web-server by using the container exec command. To list the files under the content directory, run an ls command:

% container exec my-web-server ls /content
index.html
%
If you want to poke around in the container, run a shell and issue one or more commands:

% container exec --tty --interactive my-web-server sh
/content # ls
index.html
/content # uname -a
Linux my-web-server 6.12.28 #1 SMP Tue May 20 15:19:05 UTC 2025 aarch64 Linux
/content # exit
%
The --tty and --interactive flag allow you to interact with the shell from your host terminal. The --tty flag tells the shell in the container that its input is a terminal device, and the --interactive flag connects what you input in your host terminal to the input of the shell in the container.

You will often see these two options abbreviated and specified together as -ti or -it.

Access the web server from another container
Your web server is accessible from other containers as well as from your host. Launch a second container using your web-test image, and this time, specify a curl command to retrieve the index.html content from the first container.

Note

Container relies on the new features and enhancements present in the macOS 26 beta. As a result, the functionality of accessing the web server from another container will not work on macOS 15. See https://github.com/apple/container/blob/main/docs/technical-overview.md#macos-15-limitations for more details.

container run -it --rm web-test curl http://192.168.64.3
The output should appear as:

% container run -it --rm web-test curl http://192.168.64.3
<!DOCTYPE html><html><head><title>Hello</title></head><body><h1>Hello, world!</h1></body></html>
%
If you set up the test domain earlier, you can achieve the same result with:

container run -it --rm web-test curl http://my-web-server.test
Run a published image
Push your image to a container registry, publishing it so that you and others can use it.

Publish the web server image
To publish your image, you need push images to a registry service that stores the image for future use. Typically, you need to authenticate with a registry to push an image. This example assumes that you have an account at a hypothetical registry named some-registry.example.com with username fido and a password or token my-secret, and that your personal repository name is the same as your username.

To sign into a secure registry with your login credentials, enter your username and password at the prompts after running:

container registry login some-registry.example.com
Create another name for your image that includes the registry name, your repository name, and the image name, with the tag latest:

container image tag web-test some-registry.example.com/fido/web-test:latest
Then, push the image:

container image push some-registry.example.com/fido/web-test:latest
Note

By default container is configured to use Docker Hub. You can change the default registry to another value by running container system property set registry.domain some-registry.example.com. See the other sub commands under container registry for more options.

Pull and run your image
To validate your published image, stop your current web server container, remove the image that you built, and then run using the remote image:

container stop my-web-server
container image delete web-test some-registry.example.com/fido/web-test:latest
container run --name my-web-server --detach --rm some-registry.example.com/fido/web-test:latest
Clean up
Stop your container and shut down the application.

Shut down the web server
Stop your web server container with:

container stop my-web-server
If you list all running and stopped containers, you will see that the --rm flag you supplied with the container run command caused the container to be removed:

% container list --all
ID        IMAGE                                               OS     ARCH   STATE    ADDR
buildkit  ghcr.io/apple/container-builder-shim/builder:0.0.3  linux  arm64  running  192.168.64.2
%
Stop the container service
When you want to stop container completely, run:

container system stop

How-to
How to use the features of container.

Configure memory and CPUs for your containers
Since the containers created by container are lightweight virtual machines, consider the needs of your containerized application when you use container run. The --memory and --cpus options allow you to override the default memory and CPU limits for the virtual machine. The default values are 1 gigabyte of RAM and 4 CPUs. You can use abbreviations for memory units; for example, to run a container for image big with 8 CPUs and 32 GiBytes of memory, use:

container run --rm --cpus 8 --memory 32g big
Configure memory and CPUs for large builds
When you first run container build, container starts a builder, which is a utility container that builds images from your Dockerfiles. As with anything you run with container run, the builder runs in a lightweight virtual machine, so for resource-intensive builds, you may need to increase the memory and CPU limits for the builder VM.

By default, the builder VM receives 2 GiBytes of RAM and 2 CPUs. You can change these limits by starting the builder container before running container build:

container builder start --cpus 8 --memory 32g
If your builder is already running and you need to modify the limits, just stop, delete, and restart the builder:

container builder stop
container builder delete
container builder start --cpus 8 --memory 32g
Share host files with your container
With the --volume option of container run, you can share data between the host system and one or more containers, and you can persist data across multiple container runs. The volume option allows you to mount a folder on your host to a filesystem path in the container.

This example mounts a folder named assets on your Desktop to the directory /content/assets in a container:

% ls -l ~/Desktop/assets
total 8
-rw-r--r--@ 1 fido  staff  2410 May 13 18:36 link.svg
% container run --volume ${HOME}/Desktop/assets:/content/assets docker.io/python:alpine ls -l /content/assets
total 4
-rw-r--r-- 1 root root 2410 May 14 01:36 link.svg
%
The argument to --volume in the example consists of the full pathname for the host folder and the full pathname for the mount point in the container, separated by a colon.

The --mount option uses a comma-separated key=value syntax to achieve the same result:

% container run --mount source=${HOME}/Desktop/assets,target=/content/assets docker.io/python:alpine ls -l /content/assets
total 4
-rw-r--r-- 1 root root 2410 May 14 01:36 link.svg
%
Build and run a multiplatform image
Using the project from the tutorial example, you can create an image to use both on Apple silicon Macs and on x86-64 servers.

When building the image, just add --arch options that direct the builder to create an image supporting both the arm64 and amd64 architectures:

container build --arch arm64 --arch amd64 --tag registry.example.com/fido/web-test:latest --file Dockerfile .
Try running the command uname -a with the arm64 variant of the image to see the system information that the virtual machine reports:

% container run --arch arm64 --rm registry.example.com/fido/web-test:latest uname -a
Linux 7932ce5f-ec10-4fbe-a2dc-f29129a86b64 6.1.68 #1 SMP Mon Mar 31 18:27:51 UTC 2025 aarch64 GNU/Linux
%
When you run the command with the amd64 architecture, the x86-64 version of uname runs under Rosetta translation, so that you will see information for an x86-64 system:

% container run --arch amd64 --rm registry.example.com/fido/web-test:latest uname -a
Linux c0376e0a-0bfd-4eea-9e9e-9f9a2c327051 6.1.68 #1 SMP Mon Mar 31 18:27:51 UTC 2025 x86_64 GNU/Linux
%
The command to push your multiplatform image to a registry is no different than that for a single-platform image:

container image push registry.example.com/fido/web-test:latest
Get container or image details
container image list and container list provide basic information for all of your images and containers. You can also use list and inspect commands to print detailed JSON output for one or more resources.

Use the inspect command and send the result to the jq command to get pretty-printed JSON for the images or containers that you specify:

% container image inspect web-test | jq
[
  {
    "name": "web-test:latest",
    "variants": [
      {
        "platform": {
          "os": "linux",
          "architecture": "arm64"
        },
        "config": {
          "created": "2025-05-08T22:27:23Z",
          "architecture": "arm64",
...
% container inspect my-web-server | jq
[
  {
    "status": "running",
    "networks": [
      {
        "address": "192.168.64.3/24",
        "gateway": "192.168.64.1",
        "hostname": "my-web-server.test.",
        "network": "default"
      }
    ],
    "configuration": {
      "mounts": [],
      "hostname": "my-web-server",
      "id": "my-web-server",
      "resources": {
        "cpus": 4,
        "memoryInBytes": 1073741824,
      },
...
Use the list command with the --format option to display information for all images or containers. In this example, the --all option shows stopped as well as running containers, and jq selects the IP address for each running container:

% container ls --format json --all | jq '.[] | select ( .status == "running" ) | [ .configuration.id, .networks[0].address ]'
[
  "my-web-server",
  "192.168.64.3/24"
]
[
  "buildkit",
  "192.168.64.2/24"
]
Forward traffic from localhost to your container
Use the --publish option to forward TCP or UDP traffic from your loopback IP to the container you run. The option value has the form [host-ip:]host-port:container-port[/protocol], where protocol may be tcp or udp, case insensitive.

If your container attaches to multiple networks, the ports you publish forward to the IP address of the interface attached to the first network.

To forward requests from localhost:8080 to a Python webserver on container port 8000, run:

container run -d --rm -p 127.0.0.1:8080:8000 python:slim python3 -m http.server --bind 0.0.0.0 8000
A curl to localhost:8000 outputs:

% curl http://localhost:8080                                                                                    
<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Directory listing for /</title>
</head>
<body>
<h1>Directory listing for /</h1>
<hr>
<ul>
<li><a href="bin/">bin@</a></li>
<li><a href="boot/">boot/</a></li>
<li><a href="dev/">dev/</a></li>
<li><a href="etc/">etc/</a></li>
<li><a href="home/">home/</a></li>
<li><a href="lib/">lib@</a></li>
<li><a href="lost%2Bfound/">lost+found/</a></li>
<li><a href="media/">media/</a></li>
<li><a href="mnt/">mnt/</a></li>
<li><a href="opt/">opt/</a></li>
<li><a href="proc/">proc/</a></li>
<li><a href="root/">root/</a></li>
<li><a href="run/">run/</a></li>
<li><a href="sbin/">sbin@</a></li>
<li><a href="srv/">srv/</a></li>
<li><a href="sys/">sys/</a></li>
<li><a href="tmp/">tmp/</a></li>
<li><a href="usr/">usr/</a></li>
<li><a href="var/">var/</a></li>
</ul>
<hr>
</body>
</html>
Mount your host SSH authentication socket in your container
Use the --ssh option to mount the macOS SSH authentication socket into your container, so that you can clone private git repositories and perform other tasks requiring passwordless SSH authentication.

When you use --ssh, it performs the equivalent of the options --volume "${SSH_AUTH_SOCK}:/run/host-services/ssh-auth.sock" --env SSH_AUTH_SOCK=/run/host-services/ssh-auth.sock". The added benefit of --ssh is that when you stop your container, log out, log back in, and restart your container, the system automatically updates the target path for the socket mount to the new value of SSH_AUTH_SOCK, so that socket forwarding continues to function.

% container run -it --rm --ssh alpine:latest sh 
/ # env
SHLVL=1
HOME=/root
TERM=xterm
PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin
SSH_AUTH_SOCK=/run/host-services/ssh-auth.sock
PWD=/
/ # apk add openssh-client
(1/6) Installing openssh-keygen (10.0_p1-r7)
(2/6) Installing ncurses-terminfo-base (6.5_p20250503-r0)
(3/6) Installing libncursesw (6.5_p20250503-r0)
(4/6) Installing libedit (20250104.3.1-r1)
(5/6) Installing openssh-client-common (10.0_p1-r7)
(6/6) Installing openssh-client-default (10.0_p1-r7)
Executing busybox-1.37.0-r18.trigger
OK: 12 MiB in 22 packages
/ # ssh-add -l
...auth key output...
/ # apk add git
(1/12) Installing brotli-libs (1.1.0-r2)
(2/12) Installing c-ares (1.34.5-r0)
(3/12) Installing libunistring (1.3-r0)
(4/12) Installing libidn2 (2.3.7-r0)
(5/12) Installing nghttp2-libs (1.65.0-r0)
(6/12) Installing libpsl (0.21.5-r3)
(7/12) Installing zstd-libs (1.5.7-r0)
(8/12) Installing libcurl (8.14.1-r1)
(9/12) Installing libexpat (2.7.1-r0)
(10/12) Installing pcre2 (10.43-r1)
(11/12) Installing git (2.49.1-r0)
(12/12) Installing git-init-template (2.49.1-r0)
Executing busybox-1.37.0-r18.trigger
OK: 24 MiB in 34 packages
/ # git clone git@github.com:some-org/some-private-repo.git
Cloning into 'some-private-repo'...
...
Create and use a separate isolated network
Note

This feature is available on macOS 26 and later.

Running container system start creates a vmnet network named default to which your containers will attach unless you specify otherwise.

You can create a separate isolated network using container network create.

This command creates a network named foo:

container network create foo
The foo network, the default network, and any other networks you create are isolated from one another. A container on one network has no connectivity to containers on other networks.

Run container network list to see the networks that exist:

% container network list
NETWORK  STATE    SUBNET
default  running  192.168.64.0/24
foo      running  192.168.65.0/24
%
Run a container that is attached to that network using the --network flag:

container run -d --name my-web-server --network foo --rm web-test
Use container ls to see that the container is on the foo subnet:

 % container ls
ID             IMAGE            OS     ARCH   STATE    ADDR
my-web-server  web-test:latest  linux  arm64  running  192.168.65.2
You can delete networks that you create once no containers are attached:

container stop my-web-server
container network delete foo
View container logs
The container logs command displays the output from your containerized application:

% container run -d --name my-web-server --rm registry.example.com/fido/web-test:latest
my-web-server
% curl http://my-web-server.test
<!DOCTYPE html><html><head><title>Hello</title></head><body><h1>Hello, world!</h1></body></html>
% container logs my-web-server
192.168.64.1 - - [15/May/2025 03:00:03] "GET / HTTP/1.1" 200 -
%
Use the --boot option to see the logs for the virtual machine boot and init process:

% container logs --boot my-web-server
[    0.098284] cacheinfo: Unable to detect cache hierarchy for CPU 0
[    0.098466] random: crng init done
[    0.099657] brd: module loaded
[    0.100707] loop: module loaded
[    0.100838] virtio_blk virtio2: 1/0/0 default/read/poll queues
[    0.101051] virtio_blk virtio2: [vda] 1073741824 512-byte logical blocks (550 GB/512 GiB)
...
[    0.127467] EXT4-fs (vda): mounted filesystem without journal. Quota mode: disabled.
[    0.127525] VFS: Mounted root (ext4 filesystem) readonly on device 254:0.
[    0.127635] devtmpfs: mounted
[    0.127773] Freeing unused kernel memory: 2816K
[    0.143252] Run /sbin/vminitd as init process
2025-05-15T02:24:08+0000 info vminitd : [vminitd] vminitd booting...
2025-05-15T02:24:08+0000 info vminitd : [vminitd] serve vminitd api
2025-05-15T02:24:08+0000 debug vminitd : [vminitd] starting process supervisor
2025-05-15T02:24:08+0000 debug vminitd : port=1024 [vminitd] booting grpc server on vsock
...
2025-05-15T02:24:08+0000 debug vminitd : exits=[362: 0] pid=363 [vminitd] checking for exit of managed process
2025-05-15T02:24:08+0000 debug vminitd : [vminitd] waiting on process my-web-server
[    1.122742] IPv6: ADDRCONF(NETDEV_CHANGE): eth0: link becomes ready
2025-05-15T02:24:39+0000 debug vminitd : sec=1747275879 usec=478412 [vminitd] setTime
%
Expose virtualization capabilities to a container
Note

This feature requires a M3 or newer Apple silicon machine and a Linux kernel that supports virtualization. For a kernel configuration that has all of the right features enabled, see https://github.com/apple/containerization/blob/0.5.0/kernel/config-arm64#L602.

You can enable virtualization capabilities in containers by using the --virtualization option of container run and container create.

If your machine does not have support for nested virtualization, you will see the following:

container run --name nested-virtualization --virtualization --kernel /path/to/a/kernel/with/virtualization/support --rm ubuntu:latest sh -c "dmesg | grep kvm"
Error: unsupported: "nested virtualization is not supported on the platform"
When nested virtualization is enabled successfully, dmesg will show output like the following:

container run --name nested-virtualization --virtualization --kernel /path/to/a/kernel/with/virtualization/support --rm ubuntu:latest sh -c "dmesg | grep kvm"
[    0.017245] kvm [1]: IPA Size Limit: 40 bits
[    0.017499] kvm [1]: GICv3: no GICV resource entry
[    0.017501] kvm [1]: disabling GICv2 emulation
[    0.017506] kvm [1]: GIC system register CPU interface enabled
[    0.017685] kvm [1]: vgic interrupt IRQ9
[    0.017893] kvm [1]: Hyp mode initialized successfully
Configure system properties
The container system property subcommand manages the configuration settings for the container CLI and services. You can customize various aspects of container behavior, including build settings, default images, and network configuration.

Use container system property list to show information for all available properties:

% bin/container system property ls
ID                 TYPE    VALUE                                     DESCRIPTION
build.rosetta      Bool    true                                      Build amd64 images on arm64 using Rosetta, instead of QEMU.
dns.domain         String  *undefined*                               If defined, the local DNS domain to use for containers with unqualified names.
image.builder      String  ghcr.io/apple/container-builder-shim/...  The image reference for the utility container that `container build` uses.
image.init         String  ghcr.io/apple/containerization/vminit...  The image reference for the default initial filesystem image.
kernel.binaryPath  String  opt/kata/share/kata-containers/vmlinu...  If the kernel URL is for an archive, the archive member pathname for the kernel file.
kernel.url         String  https://github.com/kata-containers/ka...  The URL for the kernel file to install, or the URL for an archive containing the kernel file.
network.subnet     String  *undefined*                               Default subnet for IP allocation (used on macOS 15 only).
Example: Disable Rosetta for builds
If you want to prevent the use of Rosetta translation during container builds on Apple Silicon Macs:

container system property set build.rosetta false
This is useful when you want to ensure builds only produce native arm64 images and avoid any x86_64 emulation.

View system logs
The container system logs command allows you to look at the log messages that container writes:

% container system logs | tail -8
2025-06-02 16:46:11.560780-0700 0xf6dc5    Info        0x0                  61684  0    container-apiserver: [com.apple.container:APIServer] Registering plugin [id=com.apple.container.container-runtime-linux.my-web-server]
2025-06-02 16:46:11.699095-0700 0xf6ea8    Info        0x0                  61733  0    container-runtime-linux: [com.apple.container:RuntimeLinuxHelper] starting container-runtime-linux [uuid=my-web-server]
2025-06-02 16:46:11.699125-0700 0xf6ea8    Info        0x0                  61733  0    container-runtime-linux: [com.apple.container:RuntimeLinuxHelper] configuring XPC server [uuid=my-web-server]
2025-06-02 16:46:11.700908-0700 0xf6ea8    Info        0x0                  61733  0    container-runtime-linux: [com.apple.container:RuntimeLinuxHelper] starting XPC server [uuid=my-web-server]
2025-06-02 16:46:11.703028-0700 0xf6ea8    Info        0x0                  61733  0    container-runtime-linux: [com.apple.container:RuntimeLinuxHelper] `bootstrap` xpc handler [uuid=my-web-server]
2025-06-02 16:46:11.720836-0700 0xf6dc3    Info        0x0                  61689  0    container-network-vmnet: [com.apple.container:NetworkVmnetHelper] allocated attachment [hostname=my-web-server.test.] [address=192.168.64.2/24] [gateway=192.168.64.1] [id=default]
2025-06-02 16:46:12.293193-0700 0xf6eaa    Info        0x0                  61733  0    container-runtime-linux: [com.apple.container:RuntimeLinuxHelper] `start` xpc handler [uuid=my-web-server]
2025-06-02 16:46:12.368723-0700 0xf6e93    Info        0x0                  61684  0    container-apiserver: [com.apple.container:APIServer] Handling container my-web-server Start.
%
Setup shell completion
The container --generate-completion-script [zsh|bash|fish] command generates completion scripts for the provided shell.

A detailed guide on how to install the completion scripts can be found here

Technical Overview
A brief description and technical overview of container.

What are containers?
Containers are a way to package an application and its dependencies into a single unit. At runtime, containers provide isolation from the host machine as well as other colocated containers, allowing applications to run securely and efficiently in a wide variety of environments.

Containerization is an important server-side technology that is used throughout the software lifecycle:

Backend developers use containers on their personal systems to create predictable execution environments for applications, and to develop and test their applications under conditions that better approximate how they would run in the datacenter.
Continuous integration and deployment (CI/CD) systems use containerization to perform reproducible builds of applications, package the results as deployable images, and deploy them to the datacenter.
Datacenters run container orchestration platforms that use the images to run containerized applications in a reliable, highly available compute cluster.
None of this workflow would be practical without ensuring interoperability between different container implementations. The Open Container Initiative (OCI) creates and maintains these standards for container images and runtimes.

How does container run my container?
Many operating systems support containers, but the most commonly encountered containers are those that run on the Linux operating system. On macOS, the typical way to run Linux containers is to launch a Linux virtual machine (VM) that hosts all of your containers.

container runs containers differently. Using the open source Containerization package, it runs a lightweight VM for each container that you create. This approach has the following properties:

Security: Each container has the isolation properties of a full VM, using a minimal set of core utilities and dynamic libraries to reduce resource utilization and attack surface.
Privacy: When sharing host data using container, you mount only necessary data into each VM. With a shared VM, you need to mount all data that you may ever want to use into the VM, so that it can be mounted selectively into containers.
Performance: Containers created using container require less memory than full VMs, with boot times that are comparable to containers running in a shared VM.
Since container consumes and produces standard OCI images, you can easily build with and run images produced by other container applications, and the images that you build will run everywhere.

container and the underlying Containerization package integrate with many of the key technologies and frameworks of macOS:

The Virtualization framework for managing Linux virtual machines and their attached devices.
The vmnet framework for managing the virtual network to which the containers attach.
XPC for interprocess communication.
Launchd for service management.
Keychain services for access to registry credentials.
The unified logging system for application logging.
You use the container command line interface (CLI) to start and manage your containers, build container images, and transfer images from and to OCI container registries. The CLI uses a client library that communicates with container-apiserver and its helpers.

The container-apiserver is a launch agent that launches when you run the container system start command, and terminates when you run container system stop. It provides the client APIs for managing container and network resources.

When container-apiserver starts, it launches an XPC helper container-core-images that exposes an API for image management and manages the local content store, and another XPC helper container-network-vmnet for the virtual network. For each container that you create, container-apiserver launches a container runtime helper container-runtime-linux that exposes the management API for that specific container.

diagram showing container functional organization

What limitations does container have today?
With the initial release of container, you get basic facilities for building and running containers, but many common containerization features remain to be implemented. Consider contributing new features and bug fixes to container and the Containerization projects!

Container to host networking
In the initial release, there is no way to route traffic directly from a client in a container to a host-based application listening on the loopback interface at 127.0.0.1. If you were to configure the application in your container to connect to 127.0.0.1 or localhost, requests would simply go to the loopback interface in the container, rather than your host-based service.

You can work around this limitation by configuring the host-based application to listen on the wildcard address 0.0.0.0, but this practice is insecure and not recommended because, without firewall rules, this exposes the application to external requests.

A more secure approach uses socat to redirect traffic from the container network gateway to the host-based service. For example, to forward traffic for port 8000, configure your containerized application to connect to 192.168.64.1:8000 instead of 127.0.0.1:8000, and then run the following command in a terminal on your Mac to forward the port traffic from the gateway to the host:

socat TCP-LISTEN:8000,fork,bind=192.168.64.1 TCP:127.0.0.1:8000
Releasing container memory to macOS
The macOS Virtualization framework implements only partial support for memory ballooning, which is a technology that allows virtual machines to dynamically use and relinquish host memory. When you create a container, the underlying virtual machine only uses the amount of memory that the containerized application needs. For example, you might start a container using the option --memory 16g, but see that the application is only using 2 GiBytes of RAM in the macOS Activity Monitor.

Currently, memory pages freed to the Linux operating system by processes running in the container's VM are not relinquished to the host. If you run many memory-intensive containers, you may need to occasionally restart them to reduce memory utilization.

macOS 15 limitations
container relies on the new features and enhancements present in the macOS 26 beta. You can run container on macOS 15, but you will need to be aware of some user experience and functional limitations. There is no plan to address issues found with macOS 15 that cannot be reproduced in the macOS 26 beta.

Network isolation
The vmnet framework in macOS 15 can only provide networks where the attached containers are isolated from one another. Container-to-container communication over the virtual network is not possible.

Multiple networks
In macOS 15, all containers attach to the default vmnet network. The container network commands are not available on macOS 15, and using the --network option for container run or container create will result in an error.

Container IP addresses
In macOS 15, limitations in the vmnet framework mean that the container network can only be created when the first container starts. Since the network XPC helper provides IP addresses to containers, and the helper has to start before the first container, it is possible for the network helper and vmnet to disagree on the subnet address, resulting in containers that are completely cut off from the network.

Normally, vmnet creates the container network using the CIDR address 192.168.64.1/24, and on macOS 15, container defaults to using this CIDR address in the network helper. To diagnose and resolve issues stemming from a subnet address mismatch between vmnet and the network helper:

Before creating the first container, scan the output of the command ifconfig for a bridge interface named similarly to bridge100.
After creating the first container, run ifconfig again, and locate the new bridge interface to determine the container subnet address.
Run container ls to check the IP address given to the container by the network helper. If the address corresponds to a different network:
Run container system stop to terminate the services for container.
Using the macOS defaults command, update the default subnet value used by the network helper process. For example, if the bridge address shown by ifconfig is 192.168.66.1, run:
defaults write com.apple.container.defaults network.subnet 192.168.66.1/24
Run container system start to launch services again.
Try running the container again and verify that its IP address matches the current bridge interface value.


Container CLI Command Reference
Note: Command availability may vary depending on host operating system and macOS version.

Core Commands
container run
Runs a container from an image. If a command is provided, it will execute inside the container; otherwise the image's default command runs. By default the container runs in the foreground and STDIN remains closed unless -i/--interactive is specified.

Usage

container run [OPTIONS] IMAGE [COMMAND] [ARG...]
Options

Process options
-e, --env <env>: Set environment variables (format: key=value)
--env-file <env-file>: Read in a file of environment variables (key=value format, ignores # comments and blank lines)
--gid <gid>: Set the group ID for the process
-i, --interactive: Keep the standard input open even if not attached
-t, --tty: Open a TTY with the process
-u, --user <user>: Set the user for the process (format: name|uid[:gid])
--uid <uid>: Set the user ID for the process
-w, --workdir, --cwd <dir>: Set the initial working directory inside the container
Resource options
-c, --cpus <cpus>: Number of CPUs to allocate to the container
-m, --memory <memory>: Amount of memory (1MiByte granularity), with optional K, M, G, T, or P suffix
Management options
-a, --arch <arch>: Set arch if image can target multiple architectures (default: arm64)
--cidfile <cidfile>: Write the container ID to the path provided
-d, --detach: Run the container and detach from the process
--dns <ip>: DNS nameserver IP address
--dns-domain <domain>: Default DNS domain
--dns-option <option>: DNS options
--dns-search <domain>: DNS search domains
--entrypoint <cmd>: Override the entrypoint of the image
-k, --kernel <path>: Set a custom kernel path
-l, --label <label>: Add a key=value label to the container
--mount <mount>: Add a mount to the container (format: type=<>,source=<>,target=<>,readonly)
--name <name>: Use the specified name as the container ID
--network <network>: Attach the container to a network
--no-dns: Do not configure DNS in the container
--os <os>: Set OS if image can target multiple operating systems (default: linux)
-p, --publish <spec>: Publish a port from container to host (format: [host-ip:]host-port:container-port[/protocol])
--platform <platform>: Platform for the image if it's multi-platform. This takes precedence over --os and --arch
--publish-socket <spec>: Publish a socket from container to host (format: host_path:container_path)
--rm, --remove: Remove the container after it stops
--ssh: Forward SSH agent socket to container
--tmpfs <tmpfs>: Add a tmpfs mount to the container at the given path
-v, --volume <volume>: Bind mount a volume into the container
--virtualization: Expose virtualization capabilities to the container (requires host and guest support)
Registry options
--scheme <scheme>: Scheme to use when connecting to the container registry. One of (http, https, auto) (default: auto)
Progress options
--disable-progress-updates: Disable progress bar updates
Global options
--debug: Enable debug output [environment: CONTAINER_DEBUG]
--version: Show the version.
-h, --help: Show help information.
Examples

# run a container and attach an interactive shell
container run -it ubuntu:latest /bin/bash

# run a background web server
container run -d --name web -p 8080:80 nginx:latest

# set environment variables and limit resources
container run -e NODE_ENV=production --cpus 2 --memory 1G node:18
container build
Builds an OCI image from a local build context. It reads a Dockerfile (default Dockerfile) and produces an image tagged with -t option. The build runs in isolation using BuildKit, and resource limits may be set for the build process itself.

Usage

container build [OPTIONS] [CONTEXT-DIR]
Arguments

CONTEXT-DIR: Build directory (default: .)
Options

-a, --arch <value>: Add the architecture type to the build
--build-arg <key=val>: Set build-time variables
-c, --cpus <cpus>: Number of CPUs to allocate to the builder container (default: 2)
-f, --file <path>: Path to Dockerfile (default: Dockerfile)
-l, --label <key=val>: Set a label
-m, --memory <memory>: Amount of builder container memory (1MiByte granularity), with optional K, M, G, T, or P suffix (default: 2048MB)
--no-cache: Do not use cache
-o, --output <value>: Output configuration for the build (format: type=<oci|tar|local>[,dest=]) (default: type=oci)
--os <value>: Add the OS type to the build
--platform <platform>: Add the platform to the build (takes precedence over --os and --arch)
--progress <type>: Progress type (format: auto|plain|tty)] (default: auto)
-q, --quiet: Suppress build output
-t, --tag <name>: Name for the built image
--target <stage>: Set the target build stage
--vsock-port <port>: Builder shim vsock port (default: 8088)
--version: Show the version.
-h, --help: Show help information.
Examples

# build an image and tag it as my-app:latest
container build -t my-app:latest .

# use a custom Dockerfile
container build -f docker/Dockerfile.prod -t my-app:prod .

# pass build args
container build --build-arg NODE_VERSION=18 -t my-app .

# build the production stage only and disable cache
container build --target production --no-cache -t my-app:prod .
Container Management
container create
Creates a container from an image without starting it. This command accepts most of the same process/resource/management flags as container run, but leaves the container stopped after creation.

Usage

container create [OPTIONS] IMAGE [COMMAND] [ARG...]
Typical use: create a container to inspect or modify its configuration before running it.

container start
Starts a stopped container. You can attach to the container's output streams and optionally keep STDIN open.

Usage

container start [OPTIONS] CONTAINER-ID
Arguments

CONTAINER-ID: Container ID
Options

-a, --attach: Attach STDOUT/STDERR
-i, --interactive: Attach STDIN
--debug: Enable debug output [environment: CONTAINER_DEBUG]
--version: Show the version.
-h, --help: Show help information.
container stop
Stops running containers gracefully by sending a signal. A timeout can be specified before a SIGKILL is issued. If no containers are specified, nothing is stopped unless --all is used.

Usage

container stop [OPTIONS] [CONTAINER-IDS...]
Arguments

CONTAINER-IDS: Container IDs
Options

-a, --all: Stop all running containers
-s, --signal <signal>: Signal to send the containers (default: SIGTERM)
-t, --time <time>: Seconds to wait before killing the containers (default: 5)
--debug: Enable debug output [environment: CONTAINER_DEBUG]
--version: Show the version.
-h, --help: Show help information.
container kill
Immediately kills running containers by sending a signal (defaults to KILL). Use with caution: it does not allow for graceful shutdown.

Usage

container kill [OPTIONS] [CONTAINER-IDS...]
Arguments

CONTAINER-IDS: Container IDs
Options

-a, --all: Kill or signal all running containers
-s, --signal <signal>: Signal to send to the container(s) (default: KILL)
--debug: Enable debug output [environment: CONTAINER_DEBUG]
--version: Show the version.
-h, --help: Show help information.
container delete (rm)
Removes one or more containers. If the container is running, you may force deletion with --force. Without a container ID, nothing happens unless --all is supplied.

Usage

container delete [OPTIONS] [CONTAINER-IDS...]
Arguments

CONTAINER-IDS: Container IDs
Options

-a, --all: Remove all containers
-f, --force: Force the removal of one or more running containers
--debug: Enable debug output [environment: CONTAINER_DEBUG]
--version: Show the version.
-h, --help: Show help information.
container list (ls)
Lists containers. By default only running containers are shown. Output can be formatted as a table or JSON.

Usage

container list [OPTIONS]
Options

-a, --all: Show stopped containers as well
--format <format>: Format of the output (values: json, table; default: table)
-q, --quiet: Only output the container ID
--debug: Enable debug output [environment: CONTAINER_DEBUG]
--version: Show the version.
-h, --help: Show help information.
container exec
Executes a command inside a running container. It uses the same process flags as container run to control environment, user, and TTY settings.

Usage

container exec [OPTIONS] CONTAINER-ID ARGUMENTS...
Arguments

CONTAINER-ID: Container ID
ARGUMENTS: New process arguments
Process flags

-e, --env <env>: Set environment variables (format: key=value)
--env-file <env-file>: Read in a file of environment variables (key=value format, ignores # comments and blank lines)
--gid <gid>: Set the group ID for the process
-i, --interactive: Keep the standard input open even if not attached
-t, --tty: Open a TTY with the process
-u, --user <user>: Set the user for the process (format: name|uid[:gid])
--uid <uid>: Set the user ID for the process
-w, --workdir, --cwd <dir>: Set the initial working directory inside the container
Options

--debug: Enable debug output [environment: CONTAINER_DEBUG]
--version: Show the version.
-h, --help: Show help information.
container logs
Fetches logs from a container. You can follow the logs (-f/--follow), restrict the number of lines shown, or view boot logs.

Usage

container logs [OPTIONS] CONTAINER-ID
Arguments

CONTAINER-ID: Container ID
Options

--boot: Display the boot log for the container instead of stdio
-f, --follow: Follow log output
-n <n>: Number of lines to show from the end of the logs. If not provided this will print all of the logs
--debug: Enable debug output [environment: CONTAINER_DEBUG]
--version: Show the version.
-h, --help: Show help information.
container inspect
Displays detailed container information in JSON. Pass one or more container IDs to inspect multiple containers.

Usage

container inspect [OPTIONS] CONTAINER-IDS...
Arguments

CONTAINER-IDS: Container IDs
Options

--debug: Enable debug output [environment: CONTAINER_DEBUG]
--version: Show the version.
-h, --help: Show help information.
Image Management
container image list (ls)
Lists local images. Verbose output provides additional details such as image ID, creation time and size; JSON output provides the same data in machine-readable form.

Usage

container image list [OPTIONS]
Options

-q, --quiet: Only output the image name
-v, --verbose: Verbose output
--format <format>: Format of the output (values: json, table; default: table)
Global: --debug, --version, -h/--help
container image pull
Pulls an image from a registry. Supports specifying a platform and controlling progress display.

Usage

container image pull [OPTIONS] REFERENCE
Options

--platform <platform>: Platform string in the form os/arch/variant. Example linux/arm64/v8, linux/amd64. Default: current host platform.
--scheme <scheme>: Scheme to use when connecting to the container registry. One of (http, https, auto) (default: auto)
--disable-progress-updates: Disable progress bar updates
Global: --debug, --version, -h/--help
container image push
Pushes an image to a registry. The flags mirror those for image pull with the addition of specifying a platform for multi-platform images.

Usage

container image push [OPTIONS] REFERENCE
Options

--platform <platform>: Platform string in the form os/arch/variant. Example linux/arm64/v8, linux/amd64 (optional)
--scheme <scheme>: Scheme to use when connecting to the container registry. One of (http, https, auto) (default: auto)
--disable-progress-updates: Disable progress bar updates
Global: --debug, --version, -h/--help
container image save
Saves an image to a tar archive on disk. Useful for exporting images for offline transport.

Usage

container image save [OPTIONS] REFERENCE
Options

--platform <platform>: Platform string in the form os/arch/variant. Example linux/arm64/v8, linux/amd64 (optional)
-o, --output <file>: Path to save the image tar archive
Global: --debug, --version, -h/--help
container image load
Loads images from a tar archive created by image save. The tar file must be specified via --input.

Usage

container image load [OPTIONS]
Options

-i, --input <file>: Path to the tar archive to load images from
Global: --debug, --version, -h/--help
container image tag
Applies a new tag to an existing image. The original image reference remains unchanged.

Usage

container image tag SOURCE_IMAGE[:TAG] TARGET_IMAGE[:TAG]
No extra flags aside from global options.

container image delete (rm)
Removes one or more images. If no images are provided, --all can be used to remove all images. Images currently referenced by running containers cannot be deleted without first removing those containers.

Usage

container image delete [OPTIONS] [IMAGE...]
Options

-a, --all: remove all images
Global: --debug, --version, -h/--help
container image prune
Removes unused (dangling) images to reclaim disk space. The command outputs the amount of space freed after deletion.

Usage

container image prune [OPTIONS]
No extra options; uses global flags for debug and help.

container image inspect
Shows detailed information for one or more images in JSON format. Accepts image names or IDs.

Usage

container image inspect [OPTIONS] IMAGE...
Only global flags (--debug, --version, -h/--help) are available.

Builder Management
The builder commands manage the BuildKit-based builder used for image builds.

container builder start
Starts the BuildKit builder container. CPU and memory limits can be set for the builder.

Usage

container builder start [OPTIONS]
Options

-c, --cpus <number>: Number of CPUs to allocate to the container (default: 2)
-m, --memory <size>: Amount of memory in bytes, kilobytes (K), megabytes (M), or gigabytes (G) for the container, with MB granularity (for example, 1024K will result in 1MB being allocated for the container) (default: 2048MB)
Global: --version, -h/--help
container builder status
Shows the current status of the BuildKit builder. Without flags a human-readable table is displayed; with --json the status is returned as JSON.

Usage

container builder status [OPTIONS]
Options

--json: output status as JSON
Global: --version, -h/--help
container builder stop
Stops the BuildKit builder. No additional options are required; uses global flags only.

container builder delete (rm)
Removes the BuildKit builder container. It can optionally force deletion if the builder is still running.

Usage

container builder delete [OPTIONS]
Options

-f, --force: force deletion even if the builder is running
Global: --version, -h/--help
Network Management (macOS 26+)
The network commands are available on macOS 26 and later and allow creation and management of user-defined container networks.

container network create
Creates a new network with the given name.

Usage

container network create NAME [OPTIONS]
Options

--label <key=value>: set metadata labels on the network
Global: --version, -h/--help
container network delete (rm)
Deletes one or more networks. When deleting multiple networks, pass them as separate arguments. To delete all networks, use --all.

Usage

container network delete [OPTIONS] [NAME...]
Options

-a, --all: delete all defined networks
Global: --debug, --version, -h/--help
container network list (ls)
Lists user-defined networks.

Usage

container network list [OPTIONS]
Options

-q, --quiet: Only output the network name
--format <format>: Format of the output (values: json, table; default: table)
Global: --debug, --version, -h/--help
container network inspect
Shows detailed information about one or more networks.

Usage

container network inspect [OPTIONS] NAME...
Only global flags are available for debugging, version, and help.

Volume Management
Manage persistent volumes for containers.

container volume create
Creates a new volume with an optional size and driver-specific options.

Usage

container volume create [OPTIONS] NAME
Options

-s <size>: size of the volume (default: 512GB). Examples: 1G, 512MB, 2T
--opt <key=value>: set driver-specific options
--label <key=value>: set metadata labels on the volume
Global: --version, -h/--help
container volume delete (rm)
Removes one or more volumes by name.

Usage

container volume delete NAME...
Only global flags are available.

container volume list (ls)
Lists volumes.

Usage

container volume list [OPTIONS]
Options

-q, --quiet: Only display volume names
--format <format>: Format of the output (values: json, table; default: table)
Global: --version, -h/--help
container volume inspect
Displays detailed information for one or more volumes in JSON.

Usage

container volume inspect NAME...
Only global flags are available.

Registry Management
The registry commands manage authentication and defaults for container registries.

container registry login
Authenticates with a registry. Credentials can be provided interactively or via flags. The login is stored for reuse by subsequent commands.

Usage

container registry login [OPTIONS] SERVER
Options

-u, --username <username>: username for the registry
--password-stdin: read the password from STDIN (non-interactive)
--scheme <scheme>: registry scheme. One of (http, https, auto) (default: auto)
Global: --version, -h/--help
container registry logout
Logs out of a registry, removing stored credentials.

Usage

container registry logout SERVER
Only --version and -h/--help are available.

System Management
System commands manage the container apiserver, logs, DNS settings and kernel. These are only available on macOS hosts.

container system start
Starts the container services and (optionally) installs a default kernel. It will start the container-apiserver and background services.

Usage

container system start [OPTIONS]
Options

-a, --app-root <path>: application data directory
--install-root <path>: path to the installation root directory
--debug: enable debug logging for the runtime daemon
--enable-kernel-install: install the recommended default kernel
--disable-kernel-install: skip installing the default kernel If neither kernel-install flag is provided, you will be prompted to choose whether to install the recommended kernel.
container system stop
Stops the container services and deregisters them from launchd. You can specify a prefix to target services created with a different launchd prefix.

Usage

container system stop [OPTIONS]
Options

-p, --prefix <prefix>: launchd prefix (default: com.apple.container.)
Global: --version, -h/--help
container system status
Checks whether the container services are running and prints status information. It will ping the apiserver and report readiness.

Usage

container system status [OPTIONS]
Options

-p, --prefix <prefix>: launchd prefix to query (default: com.apple.container.)
Global: --version, -h/--help
container system logs
Displays logs from the container services. You can specify a time interval or follow new logs in real time.

Usage

container system logs [OPTIONS]
Options

--last <duration>: Fetch logs starting from the specified time period (minus the current time); supported formats: m, h, d (default: 5m)
-f, --follow: Follow log output
Global: --debug, --version, -h/--help
container system dns create
Creates a local DNS domain for containers. Requires administrator privileges (use sudo).

Usage

container system dns create NAME
No options.

container system dns delete (rm)
Deletes a local DNS domain. Requires administrator privileges (use sudo).

Usage

container system dns delete NAME
No options.

container system dns list (ls)
Lists configured local DNS domains for containers.

Usage

container system dns list
No options.

container system kernel set
Installs or updates the Linux kernel used by the container runtime on macOS hosts.

Usage

container system kernel set [OPTIONS]
Options

--binary <path>: Path to a kernel binary (can be used with --tar inside a tar archive)
--tar <path | URL>: Path or URL to a tarball containing kernel images
--arch <arch>: Target architecture (arm64 or x86_64)
--recommended: Download and install the recommended default kernel for your host
Global: --debug, --version, -h/--help
container system property list (ls)
Lists all available system properties with their current values, types, and descriptions. Output can be formatted as a table or JSON.

Usage

container system property list [OPTIONS]
Options

-q, --quiet: Only output the property IDs
--format <format>: Format of the output (values: json, table; default: table)
Global: --debug, --version, -h/--help
Examples

# list all properties in table format
container system property list

# get only property IDs
container system property list --quiet

# output as JSON for scripting
container system property list --format json
container system property get
Retrieves the current value of a specific system property by its ID.

Usage

container system property get PROPERTY_ID
Arguments

PROPERTY_ID: The ID of the property to retrieve (use property list to see available IDs)
Global flags: --debug, --version, -h/--help

Examples

# get the default registry domain
container system property get registry.domain

# get the current DNS domain setting
container system property get dns.domain
container system property set
Sets the value of a system property. The command validates the value based on the property type (boolean, domain name, image reference, URL, or CIDR address).

Usage

container system property set PROPERTY_ID VALUE
Arguments

PROPERTY_ID: The ID of the property to set
VALUE: The new value for the property
Property Types and Validation

Boolean properties (build.rosetta): Accepts true, t, false, f (case-insensitive)
Domain properties (dns.domain, registry.domain): Must be valid domain names
Image properties (image.builder, image.init): Must be valid OCI image references
URL properties (kernel.url): Must be valid URLs
Network properties (network.subnet): Must be valid CIDR addresses
Path properties (kernel.binaryPath): Accept any string value
Global flags: --debug, --version, -h/--help

Examples

# enable Rosetta for AMD64 builds on ARM64
container system property set build.rosetta true

# set a custom DNS domain
container system property set dns.domain mycompany.local

# configure a custom registry
container system property set registry.domain registry.example.com

# set a custom builder image
container system property set image.builder myregistry.com/custom-builder:latest
container system property clear
Clears (unsets) a system property, reverting it to its default value.

Usage

container system property clear PROPERTY_ID
Arguments

PROPERTY_ID: The ID of the property to clear
Global flags: --debug, --version, -h/--help

Examples

# clear custom DNS domain (revert to default)
container system property clear dns.domain

# clear custom registry setting
container system property clear registry.domain