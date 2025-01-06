import tensorflow as tf  

hello = tf.constant("Hello, world!")

with tf.compat.v1.Session() as sess:
    result = sess.run(hello)
    print(result.decode())