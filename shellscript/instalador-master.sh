#!/bin/bash

G="\033[01;32m"
W="\033[00;37m"
R="\033[01;31m"
B="\033[01;34m"
Y="\033[01;33m"
C="\033[01;36m"
NO="\\033[1;0m"
USER=""
PWD=""


function verificaInstalacaoExistente {
	if [ -f "instalador-cliente.zip" ]; then
		echo -e "\n"
		echo -e "$R ATENÇÃO! Já existe uma instalação master nesta máquina. Caso você continue com a instalação, novas credenciais serão geradas e será necessária a reinstalação nos clientes com o novo instalador gerado. $NO"
		echo -e "\n"
		while true; do
			read -p "Você deseja continuar com a instalacão [sim/não]? " yn
			case $yn in
				[Ss]* )
					rm -f instalador-cliente.zip; break;;
				[Nn]* )
					echo -e "$R Instalação abortada! \n $NO" 
					exit;;
				* )
					echo -e "$R Você precisa informar sim ou não. $NO";;
			esac
		done

        fi
}


function verificaUsuario {
	echo -e "\n"
	if [ `whoami` == 'root' ]; then
		echo -e "$C INSTALADOR DO PAINEL - VERSÃO MASTER $NO"
	else
		echo -e "$R ERRO! Você precisa estar logado como root para executar este script... $NO"
		exit;
	fi
}


function instrucoes {
	echo -e "\n"
	echo "Você terá que informar usuário e senha da conta MYSQL que tem acesso ao banco de dados 'painel' "
	echo -e "\n"
}


function obterCredenciaisMysql {
	read -p "Usuário do mysql: " USER
	read -s -p "Senha do mysql: " PWD
	echo -e "\n"
	echo "show databases" > /tmp/show.txt
	RESULT=`mysql -u$USER -p$PWD painel < /tmp/show.txt | grep painel`
	if [ $RESULT == "painel" ] 2>/dev/null; then
		echo -e "$G Conexão com o banco de dados efetuada com sucesso...  $NO"
		`tar -xf pacotes.tar`
		echo -e "USER=$USER\nPWD=$PWD">>pacotes/coletor-master1
		`cat pacotes/coletor-master1 > pacotes/coletor-master`
		`cat pacotes/coletor-master2 >> pacotes/coletor-master`
		`cat pacotes/coletor-master0 > pacotes/coletor-master1`
	else
		echo -e "$R Ocorreu um erro ao efetuar a conexão de banco de dados! Verifique se as credenciais estão corretas e so o banco "painel" está instalado nesta máquina.  $NO"
		exit
	fi
	echo -e "\n"
}


function limparInstalacaoAntiga {
	`userdel coletorpainel 2> /dev/null > /dev/null`
	`rm -fr /home/coletorpainel/ > /dev/null 2> /dev/null`
	`rm -f /var/spool/mail/coletorpainel > /dev/null 2> /dev/null`
	`rm -f pacotes/instalador-cliente/pacotes-cliente/id_rsa.pub > /dev/null 2> /dev/null`
}


function criarUsuarioColetor {
	echo -e "$G Criando usuario e credenciais...  $NO"
	`adduser coletorpainel`
	`mkdir ~coletorpainel/.ssh/`
	`ssh-keygen -t rsa -f  ~coletorpainel/.ssh/id_rsa -q -P ""`
	echo "StrictHostKeyChecking no" > ~coletorpainel/.ssh/config
	`chown -R coletorpainel:coletorpainel  ~coletorpainel/`
	`chmod -R 400  ~coletorpainel/.ssh/*`
}


function criarPacoteInstalacaoCliente {
	echo -e "$G Criando instalador para clientes...  $NO"
	cd pacotes/instalador-cliente
	tar -xf pacotes-cliente.tar
	rm -f pacotes-cliente.tar
	`cp ~coletorpainel/.ssh/id_rsa.pub pacotes-cliente/`
	tar cf pacotes-cliente.tar pacotes-cliente
	rm -fr pacotes-cliente
	cd ../../pacotes
	zip -r ../instalador-cliente.zip instalador-cliente/ > /dev/null 2> /dev/null
	cd ..
}


function limparInstalacaoMaster {
	rm -f /usr/bin/coletor-master 2>/dev/null
}


function colocaColetorNoCrontab {
	echo -e "$G Colocando coletor do master no crontab...  $NO"
        RES=""
        RES=`cat /etc/crontab | grep coletor-master`
        if [[ $RES == "" ]]; then
                echo "0-59/1 * * * * coletorpainel sh coletor-master" >> /etc/crontab
        fi
}


function instalarColetorMaster {
	echo -e "$G Instalando coletor master...  $NO"
        limparInstalacaoMaster
	cp pacotes/coletor-master /usr/bin
        chmod 755 /usr/bin/coletor-master
	tar -cf pacotes.tar pacotes
	rm -fr pacotes
	colocaColetorNoCrontab
	echo -e "\n"
}


function iniciarInstalacao {
	limparInstalacaoAntiga
	criarUsuarioColetor
	criarPacoteInstalacaoCliente
	instalarColetorMaster
	echo -e "$G Instalação concluída! utilize o pacote 'instalador-cliente.zip' para instalação nos clientes  $NO"
	echo -e "\n"
}


function init {
	verificaUsuario
	verificaInstalacaoExistente
	instrucoes
	obterCredenciaisMysql		
	iniciarInstalacao	
}

init
