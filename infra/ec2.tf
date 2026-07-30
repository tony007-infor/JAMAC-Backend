# Busca automáticamente la última AMI oficial de Ubuntu 22.04 (Canonical)
data "aws_ami" "ubuntu" {
  most_recent = true
  owners      = ["099720109477"] # Canonical

  filter {
    name   = "name"
    values = ["ubuntu/images/hvm-ssd/ubuntu-jammy-22.04-amd64-server-*"]
  }

  filter {
    name   = "virtualization-type"
    values = ["hvm"]
  }
}

resource "aws_security_group" "backend_sg" {
  name        = "${var.app_name}-sg"
  description = "Permite SSH y trafico HTTP de la app backend"

  ingress {
    description = "SSH"
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"] # demo; en un entorno real, restringir a tu IP
  }

  ingress {
    description = "Backend app (Apache expone 8000, usamos --network host)"
    from_port   = 8000
    to_port     = 8000
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = {
    Name = "${var.app_name}-sg"
  }
}

resource "aws_instance" "backend" {
  ami                    = data.aws_ami.ubuntu.id
  instance_type          = var.instance_type
  key_name               = var.key_name
  vpc_security_group_ids = [aws_security_group.backend_sg.id]

  # Fuerza a recrear la instancia si cambia el script de arranque
  user_data_replace_on_change = true

  root_block_device {
    volume_size = 20
    volume_type = "gp3"
  }

  # Instala y configura PostgreSQL automaticamente al arrancar la maquina
  user_data = <<-EOF
    #!/bin/bash
    # v2 - instala postgres
    set -e
    apt-get update -y
    apt-get install -y postgresql postgresql-contrib

    systemctl enable postgresql
    systemctl start postgresql

    sudo -u postgres psql -c "CREATE ROLE laravel_user WITH LOGIN PASSWORD '${var.db_password}';"
    sudo -u postgres psql -c "CREATE DATABASE laravel_db OWNER laravel_user;"
    sudo -u postgres psql -c "GRANT ALL PRIVILEGES ON DATABASE laravel_db TO laravel_user;"
  EOF

  tags = {
    Name = var.app_name
  }
}