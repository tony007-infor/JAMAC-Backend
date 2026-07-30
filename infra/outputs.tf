output "public_ip" {
  description = "IP publica fija (Elastic IP) del backend"
  value       = aws_eip.backend.public_ip
}