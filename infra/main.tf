terraform {
  required_version = ">= 1.5.0"

  required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = "~> 5.0"
    }
  }

  # El bucket/región/clave se completan en `terraform init` desde el pipeline
  # (con -backend-config), así no hardcodeamos nada sensible aquí.
  backend "s3" {}
}

provider "aws" {
  region = var.aws_region
}