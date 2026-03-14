variable "project_name" {
    type = string
}

variable "environment" {
    type = string
}

variable "aws_region" {
    type = string
}

variable "vpc_id" {
    type = string
}

variable "subnet_id" {
    type = string
}

variable "instance_type" {
    type = string
}

variable "ami_id" {
    type = string
}

variable "root_volume_size_gb" {
    type = number
}

variable "ssh_allowed_cidr" {
    type = string
}

variable "public_key_path" {
    type = string
}
