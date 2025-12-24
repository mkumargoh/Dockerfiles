# Authorized by: MAnsih Kumar
# Date: 2024-06-10
# Description: Dockerfile for a Java application with multi-stage build
# =========================
# Builder Stage
FROM maven:3.8.4-openjdk-17 AS builder
WORKDIR /app
COPY pom.xml .
COPY src ./src
RUN mvn clean package -DskipTests
# =========================
# Production Stage
FROM openjdk:17-jdk-slim
WORKDIR /app
COPY --from=builder /app/target/myapp.jar .
CMD ["java", "-jar", "myapp.jar"]

# End of Dockerfile for Java application
