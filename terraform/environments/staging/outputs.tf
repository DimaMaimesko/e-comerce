output "instance_id" {
  value = module.staging_server.instance_id
}

output "public_ip" {
  value = module.staging_server.public_ip
}

output "public_dns" {
  value = module.staging_server.public_dns
}

output "ssh_command" {
  value = module.staging_server.ssh_command
}
