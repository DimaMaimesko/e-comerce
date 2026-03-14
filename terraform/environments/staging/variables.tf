variable "project_name" {
  type    = string
  default = "ecommerce"
}

variable "environment" {
  type    = string
  default = "staging"
}

variable "aws_region" {
  type    = string
  default = "us-east-1"
}

variable "instance_type" {
  type    = string
  default = "t3.medium"
}

variable "ami_id" {
  type        = string
  description = "Ubuntu AMI ID for the selected region"
}

variable "root_volume_size_gb" {
  type    = number
  default = 30
}

variable "ssh_allowed_cidr" {
  type        = string
  description = "Your IP in CIDR format, for example 203.0.113.10/32"
}

variable "public_key_path" {
  type        = string
  description = "Path to your local SSH public key"
}
