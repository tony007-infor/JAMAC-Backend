output "public_ip" {
  description = "IP publica de la instancia, usada por el job de despliegue"
  value       = aws_instance.backend.public_ip
}