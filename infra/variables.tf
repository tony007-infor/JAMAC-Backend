variable "aws_region" {
  description = "Región de AWS donde se crea la instancia"
  type        = string
}

variable "instance_type" {
  description = "Tipo de instancia EC2"
  type        = string
  default     = "t3.micro"
}

variable "key_name" {
  description = "Nombre del Key Pair de EC2 ya existente (jamac-deploy-key)"
  type        = string
  default     = "jamac-deploy-key"
}

variable "app_name" {
  description = "Nombre de la app, usado para tags"
  type        = string
  default     = "jamac-backend"
}

variable "db_password" {
  description = "Password de la base de datos que Terraform configura en la EC2"
  type        = string
  sensitive   = true
}