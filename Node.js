# Authorized by: Manish Kumar
# Date: 2024-06-10
# Description: Dockerfile for a Node.js application with multi-stage build
FROM node:18 AS builder
WORKDIR /usr/src/app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build
# Production stage
FROM node:18-alpine
WORKDIR /usr/src/app
COPY --from=builder /usr/src/app/dist ./dist
COPY package*.json ./
RUN npm install --only=production
EXPOSE 3000
CMD ["npm", "start"]
# End of Dockerfile for Node.js application
