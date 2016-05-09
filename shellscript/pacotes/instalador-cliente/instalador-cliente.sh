#!/bin/bash

G="\033[01;32m"
W="\033[00;37m"
R="\033[01;31m"
B="\033[01;34m"
Y="\033[01;33m"
C="\033[01;36m"
NO="\\033[1;0m"


function verificaUsuario {
        echo -e "\n"
        if [ `whoami` == 'root' ]; then
                echo -e "$C INSTALADOR DO PAINEL - VERSÃO CLIENTE $NO"
        else
                echo -e "$R ERRO! Você precisa estar logado como root para executar este script... $NO"
                exit;
        fi
}


function limparInstalacaoAntiga {
	`userdel coletorpainel 2> /dev/null > /dev/null`
	`rm -fr /home/coletorpainel/ > /dev/null 2> /dev/null`
	`rm -f /var/spool/mail/coletorpainel > /dev/null 2> /dev/null`
}


function instalarColetorCliente {
	tar -xf pacotes-cliente.tar
	`adduser coletorpainel`
	`mkdir ~coletorpainel/.ssh/`
	`cp pacotes-cliente/id_rsa.pub  ~coletorpainel/.ssh/authorized_keys`
	`chown -R coletorpainel:coletorpainel  ~coletorpainel/`
	`chmod -R 400  ~coletorpainel/.ssh/*`
	`cp pacotes-cliente/coletor-cliente /usr/bin/`
	`chmod 755 /usr/bin/coletor-cliente`
	rm -fr pacotes-cliente/
}


function init {
	verificaUsuario
	limparInstalacaoAntiga
	instalarColetorCliente
	 echo -e "$G Instalação concluída! Lembre de adicionar esta máquina nas configurações do painel.  $NO"
}


init
